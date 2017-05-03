<?php
/**
 * Фильтры для таблицы Пользователи
 */
class MemberFilterForm extends nomvcAbstractFilterForm{

    public function init() {
        parent::init();

        //Период
        $this->addWidget(new nomvcInputDatePeriodPickerWidget("Дата создания", "dt"));
        $this->addValidator("dt", new nomvcDatePeriodValidator());

        $this->addWidget(new nomvcInputTextWidget('Learning ID', 'learning_id'));
        $this->addValidator('learning_id', new nomvcStringValidator(array('required' => false,'min' => 1, 'max' => 50)));

        $this->addWidget(new nomvcInputTextWidget('Фамилия', 'surname'));
        $this->addValidator('surname', new nomvcStringValidator(array('required' => false,'min' => 2, 'max' => 200)));

        if ($this->context->getUser()->getAttribute('id_restaurant') == null) {
            $this->addWidget(new nomvcSelectFromMultipleDbWidget('Ресторан', 'id_restaurant', array(
                'helper' => $this->context->getDbHelper(),
                'table' => 'V_RESTAURANT',
                'order' => 'name',
                'required' => false,
                'multiple' => true
            )));

            $this->addValidator('id_restaurant', new nomvcValueInDbMultipleValidator(array(
                'required' => false,
                "helper" => $this->context->getDbHelper(),
                "table" => "V_RESTAURANT",
                "key" => "id_restaurant"
            )));
        }
        
        $this->addWidget(new nomvcSelectFromMultipleDbWidget('Должность', 'id_position', array(
            'helper' => $this->context->getDbHelper(),
            'table' => 'V_POSITION',
            'order' => 'name',
            'required' => false,
            'multiple' => true
        )));

        $this->addValidator('id_position', new nomvcValueInDbMultipleValidator(array(
            'required' => false,
            "helper" => $this->context->getDbHelper(),
            "table" => "V_POSITION",
            "key" => "id_position"
        )));
        
        //Логин
        $this->addWidget(new nomvcInputTextWidget("Логин", "login"));
        $this->addValidator('login', new nomvcStringValidator(array('required' => false, 'min' => 2, 'max' => 200)));

        //Телефон
        $this->addWidget(new nomvcInputTextWidget("Телефон", "msisdn"));
        $this->addValidator('msisdn', new nomvcStringValidator(array('required' => false, 'min' => 2, 'max' => 200)));

        //Почта
        $this->addWidget(new nomvcInputTextWidget("Почта", "email"));
        $this->addValidator('email', new nomvcStringValidator(array('required' => false, 'min' => 2, 'max' => 200)));

        $this->addWidget(new nomvcSelectFromMultipleDbWidget('Статус сотрудника', 'id_status', array(
            'helper' => $this->context->getDbHelper(),
            'table' => 'V_MEMBER_STATUS',
            'order' => 'name',
            'required' => false
        )));
        $this->addValidator('id_status', new nomvcValueInDbValidator(array('required' => false, 'helper' => $this->context->getDbHelper(), 'table' => 'V_MEMBER_STATUS', 'key' => 'id_status')));

        $this->addWidget(new nomvcSelectFromMultipleDbWidget('Статус участия в ПЛ', 'id_status_ext', array(
            'helper' => $this->context->getDbHelper(),
            'table' => 'V_MEMBER_STATUS_EXT',
            'order' => 'id_status',
            'required' => false,
            'key' => 'id_status',
            'multiple' => true
        )));
        $this->addValidator('id_status_ext', new nomvcValueInDbMultipleValidator(array(
            'required' => false, 
            'helper' => $this->context->getDbHelper(), 
            'table' => 'V_MEMBER_STATUS_EXT', 
            'key' => 'id_status'
        )));

        //Роль
        //$this->addWidget(new nomvcInputTextWidget("Роль", "roles_list"));
        //$this->addValidator('roles_list', new nomvcStringValidator(array('required' => false, 'min' => 2, 'max' => 200)));

        //Регион
        //$this->addWidget(new nomvcInputTextWidget("Регион", "geo_object_list"));
        //$this->addValidator('geo_object_list', new nomvcStringValidator(array('required' => false, 'min' => 2, 'max' => 200)));

        $this->addButton('search');
        $this->addButton('reset');
        $this->addButton('export');

        $this->addWidget(new nomvcButtonWidget('Создать пользователя', 'create', array(
            'type' => 'button',
            'icon' => 'file'
        ), array(
            'onclick' => "return TableFormActions.getForm('member');",
            'class' => 'btn btn-success'
        )));

        if ($this->context->getUser()->getAttribute('id_restaurant') == null) {
            $this->addWidget(new nomvcButtonWidget('Подгрузить список пользователей', 'import', array(
                'type' => 'button',
                'icon' => 'file'
            ), array(
                'onclick' => "return TableFormActions.getForm('member-import');",
                'class' => 'btn btn-default'
            )));
        }
    }
}
