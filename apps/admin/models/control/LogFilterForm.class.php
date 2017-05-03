<?php

class LogFilterForm extends nomvcAbstractFilterForm{
    public function init() {
        parent::init();

        //Период
        $this->addWidget(new nomvcInputDatePeriodPickerWidget("Период отправки запроса", "dt"));
        $this->addValidator("dt", new nomvcDatePeriodValidator());

        //$this->addWidget(new nomvcInputTextWidget('ID', 'id_log'));
        //$this->addValidator('id_log', new nomvcIntegerValidator(array('required' => false)));
        
        //if ($this->context->getUser()->getAttribute('id_service') == null) {
            $this->addWidget(new nomvcSelectFromMultipleDbWidget('Сервис', 'id_service', array(
                'helper' => $this->context->getDbHelper(),
                'table' => 'V_SERVICE',
                'order' => 'name',
                'required' => false,
                'multiple' => true
            )));

            $this->addValidator('id_service', new nomvcValueInDbMultipleValidator(array(
                'required' => false,
                "helper" => $this->context->getDbHelper(),
                "table" => "V_SERVICE",
                "key" => "id_service"
            )));
        //}
        
        $this->addWidget(new nomvcInputTextWidget('Имя', 'name'));
        $this->addValidator('name', new nomvcStringValidator(array('required' => false)));

        $this->addWidget(new nomvcInputTextWidget('Фамилия', 'surname'));
        $this->addValidator('surname', new nomvcStringValidator(array('required' => false)));
        
        $this->addWidget(new nomvcInputTextWidget('Отчество', 'patronymic'));
        $this->addValidator('patronymic', new nomvcStringValidator(array('required' => false)));        
        
        $this->addWidget(new nomvcInputTextWidget('Телефон', 'msisdn'));
        $this->addValidator('msisdn', new nomvcStringValidator(array('required' => false)));

        $this->addWidget(new nomvcInputTextWidget('Email', 'email'));
        $this->addValidator('email', new nomvcStringValidator(array('required' => false)));

        $this->withMemberShowFields();
         
		$this->addButton('search');
        $this->addButton('reset');
        $this->addButton('export');
    }
    
    protected function withMemberShowFields(){
        $role_list = array();
        foreach ($this->context->getUser()->getAttribute('roles') as $key => $role){
            $role_list[$key] = $role['role'];
        }
        
        if (!in_array('root', $role_list)){
            $show_fields = $this->getMemberShowFields();
            $exclude_list = array('id_log', 'id_service', 'dt', 'service');
            
            foreach ($this->widgets as $key => $column){
                if (!in_array($key, $show_fields) && !in_array($key, $exclude_list)){
                    $this->removeWidget($key);            
                }
            }
        }
    }
    
    protected function getMemberShowFields(){  
        $show_fields = array();
        $conn = $this->context->getDb();
        
        $stmt = $conn->prepare('
            select tsf.`NAME` AS `show_field`
            from `T_SHOW_FIELD` tsf 
            inner join `T_MEMBER_SHOW_FIELD` tmsf on tsf.`ID_SHOW_FIELD` = tmsf.`ID_SHOW_FIELD`
            where tmsf.`ID_MEMBER` = :id_member
        ');
                
        $stmt->bindValue('id_member', $this->context->getUser()->getAttribute('id_member'));
        $stmt->execute();
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $show_fields[] = $row['show_field'];
        }
        
        return $show_fields;
    }
}
