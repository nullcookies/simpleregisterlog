<?php

class Log2FilterForm extends nomvcAbstractFilterForm{

    protected $id_services;

    public function init() {
        parent::init();

        $this->id_services = $this->context->getUser()->getAttribute('id_services');

        $this->addWidget(new nomvcInputDatePeriodPickerWidget("Период отправки запроса", "dt"));
        $this->addValidator("dt", new nomvcDatePeriodValidator());

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

        $this->addWidget(new nomvcInputTextWidget('Email id', 'mail_email_id'));
        $this->addValidator('mail_email_id', new nomvcIntegerValidator(array('required' => false)));

        $this->addWidget(new nomvcInputTextWidget('Название события', 'mail_event_name'));
        $this->addValidator('mail_event_name', new nomvcStringValidator(array('required' => false)));

        $this->addWidget(new nomvcInputDatePeriodPickerWidget("Время события", "mail_event_time"));
        $this->addValidator("mail_event_time", new nomvcDatePeriodValidator());

        $this->addWidget(new nomvcInputTextWidget("Email to", "mail_email"));
        $this->addValidator("mail_email", new nomvcStringValidator());

        $this->addWidget(new nomvcInputTextWidget("Статус отправки", "mail_status"));
        $this->addValidator("mail_status", new nomvcStringValidator());

        $this->addWidget(new nomvcInputTextWidget("Статус отправки группы", "mail_status_group"));
        $this->addValidator("mail_status_group", new nomvcStringValidator());

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

        $this->addWidget(new nomvcInputTextWidget('company_type', 'company_type'));
        $this->addValidator('company_type', new nomvcStringValidator(array('required' => false)));

        $this->addWidget(new nomvcInputTextWidget('no_of_staff', 'no_of_staff'));
        $this->addValidator('no_of_staff', new nomvcStringValidator(array('required' => false)));

        $this->addWidget(new nomvcInputTextWidget('Город', 'city'));
        $this->addValidator('city', new nomvcStringValidator(array('required' => false)));

        $this->addWidget(new nomvcInputTextWidget('Код', 'code'));
        $this->addValidator('code', new nomvcStringValidator(array('required' => false)));

        $this->addWidget(new nomvcInputTextWidget('Номер', 'num'));
        $this->addValidator('num', new nomvcStringValidator(array('required' => false)));

        $this->withMemberShowFields();

        $services = $this->context->getUser()->getAttribute('id_services');
        if (is_array($services) && !$this->checkIsRoot()) {
            $this->addValidator('id_services_main', new nomvcArrayValidator());
        }

        $this->addButton('search');
        $this->addButton('reset');
        $this->addButton('export');
    }

    public function getMetaKeys($id_services){
        $conn = $this->context->getDb();

        $sql = '
            select 
            tssf.id_show_field,
            tsf.name,
            tsf.name_rus,
            tmk.id_meta_key,
            tmk.name as meta_key,
            tmk.meta_type
            from `T_SERVICE_SHOW_FIELD` tssf
            inner join `T_SHOW_FIELD` tsf on tssf.id_show_field = tsf.id_show_field
            inner join `T_META_KEY` tmk on tsf.id_meta_key = tmk.id_meta_key
            where tssf.`id_service` in (bind_str)
            group by 
            tssf.id_show_field,
            tsf.name,
            tsf.name_rus,
            tmk.id_meta_key,
            tmk.name,
            tmk.meta_type
            order by tssf.order_num
        ';

        $bind_str = '';
        foreach ($id_services as $key => $id_service) {
            $bind_str .= isset($id_services[$key + 1]) ? ":id_service_$key, " : ":id_service_$key";
        }

        $sql = str_replace('bind_str', $bind_str, $sql);

        $stmt = $conn->prepare($sql);
        foreach ($id_services as $key => $id_service){
            $stmt->bindValue("id_service_$key", $id_service);
        }

        $stmt->execute();

        $meta = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            foreach ($row as $key => $val){
                $row[$key] = strtolower($val);
            }

            $meta[] = $row;
        }

        return $meta;
    }

    public function addWheres($criteria, $filters) {
        $filters = array_merge($this->defaultValues, $filters);
        foreach ($this->validators as $name => $validator) {
            if ($name == 'id_services_main'){
                $whereSqlParts = array();
                $whereSqlVars = array();

                foreach ($this->context->getUser()->getAttribute('id_services') as $key => $value) {
                    $whereSqlParts[] = $arrElName = ":{$name}_{$key}";
                    $whereSqlVars[$arrElName] = $value;
                }
                $whereSqlParts = implode(', ', $whereSqlParts);
                $criteria->addWhere("id_service in ($whereSqlParts)", $whereSqlVars);
            }
            elseif (isset($this->contextMap[$name])) {
                if ($validator instanceof nomvcDatePeriodValidator
                    || $validator instanceof nomvcDateTimePeriodValidator) {
                    if (isset($filters[$name]['from']) && $filters[$name]['from'] > '') {
                        $criteria->addContext($this->contextMap[$name].'_from', $filters[$name]['from']);
                    }
                    if (isset($filters[$name]['to']) && $filters[$name]['to'] > '') {
                        $criteria->addContext($this->contextMap[$name].'_to', $filters[$name]['to']);
                    }
                } else {
                    $criteria->addContext($this->contextMap[$name], $filters[$name]);
                }
            }
            else {
                if ($validator instanceof nomvcValueInDbMultipleValidator) {
                    if (isset($filters[$name]) && count($filters[$name])) {
                        $whereSqlParts = array();
                        $whereSqlVars = array();
                        foreach ($filters[$name] as $key => $value) {
                            $whereSqlParts[] = $arrElName = ":{$name}_{$key}";
                            $whereSqlVars[$arrElName] = $value;
                        }
                        $whereSqlParts = implode(', ', $whereSqlParts);
                        $criteria->addWhere("{$name} in ($whereSqlParts)", $whereSqlVars);
                    }
                } elseif ($validator instanceof nomvcIntegerValidator
                    || $validator instanceof nomvcValueInDbValidator) {
                    if (isset($filters[$name]) && $filters[$name] !== null) {
                        $criteria->addWhere("{$name} like CONCAT('%', upper(:{$name}), '%')", array($name => $filters[$name]));
                    }
                } elseif ($validator instanceof nomvcStringValidator) {
                    if (isset($filters[$name]) && $filters[$name] !== null) {
                        $criteria->addWhere("upper({$name}) like CONCAT('%', upper(:{$name}), '%')", array($name => $filters[$name]));
                    }
                } elseif ($validator instanceof nomvcDatePeriodValidator
                    || $validator instanceof nomvcDateTimePeriodValidator) {
                    if (isset($filters[$name])) {
                        if (isset($filters[$name]['from']) && $filters[$name]['from'] > '') {
                            $criteria->addWhere("{$name} >= :{$name}_from", array($name.'_from' => date('Y-m-d H:i:s',strtotime($filters[$name]['from']))));
                        }
                        if (isset($filters[$name]['to']) && $filters[$name]['to'] > '') {
                            $criteria->addWhere("{$name} <= :{$name}_to", array($name.'_to' => date('Y-m-d 23:59:59',strtotime($filters[$name]['to']))));
                        }
                    }
                }
            }
        }
        return $filters;
    }

    protected function checkIsRoot(){
        $roles = $this->context->getUser()->getAttribute('roles');

        foreach ($roles as $role){
            if ($role['role'] == 'root')
                return true;
        }

        return false;
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

        $meta_keys = $this->getMetaKeys($this->id_services);

        $this->widgets_old = $this->widgets;

        $unset = false;
        foreach ($this->widgets as $key => $column){
            if ($unset)
                unset($this->widgets[$key]);

            if ($key == 'id_service'){
                $unset = true;
            }
        }

        foreach ($meta_keys as $key) {
            if (isset($this->widgets_old[$key['name']])) {
                $this->widgets[$key['name']] = $this->widgets_old[$key['name']];
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
