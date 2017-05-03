<?php
/**
 * Форма Пользователи, здесь указываем поля и валидаторы
 */
class MemberForm extends nomvcAbstractForm {
    public function init() {
        parent::init();
        
        $this->addWidget(new nomvcInputHiddenWidget('id_member', 'id_member'));
        $this->addValidator('id_member', new nomvcIntegerValidator(array('required' => false)));
        
        //var_dump($this->context->getUser()->getAttribute('id_restaurant')); exit;
        if ($this->context->getUser()->getAttribute('id_restaurant') == null) {
            $this->addWidget(new nomvcSelectFromMultipleDbWidget('Ресторан', 'id_restaurant', array(
                'helper' => $this->context->getDbHelper(),
                'table' => 'V_RESTAURANT',
                'order' => 'name',
                'required' => false
            )));
            $this->addValidator('id_restaurant', new nomvcValueInDbValidator(array('required' => false, 'helper' => $this->context->getDbHelper(), 'table' => 'V_RESTAURANT', 'key' => 'id_restaurant')));
        }
        
        $this->addWidget(new nomvcSelectFromMultipleDbWidget('Должность', 'id_position', array(
            'helper' => $this->context->getDbHelper(),
            'table' => 'V_POSITION',
            'order' => 'name',
            'required' => false
        )));
        $this->addValidator('id_position', new nomvcValueInDbValidator(array('required' => false, 'helper' => $this->context->getDbHelper(), 'table' => 'V_POSITION', 'key' => 'id_position')));
        
        $this->addWidget(new nomvcInputTextWidget('Фамилия', 'surname'));
        $this->addValidator('surname', new nomvcStringValidator(array('required' => true,'min' => 2, 'max' => 200)));

        $this->addWidget(new nomvcInputTextWidget('Имя', 'name'));
        $this->addValidator('name', new nomvcStringValidator(array('required' => true,'min' => 2, 'max' => 200)));
        //Отчество
        //$this->addWidget(new nomvcInputTextWidget('Отчество', 'patronymic'));
        //$this->addValidator('patronymic', new nomvcStringValidator(array('required' => false,'min' => 2, 'max' => 200)));

        //День рождения
        //$this->addWidget(new nomvcInputDateTimePickerWidget("День рождения", "day_of_birth", array("value" => date("Y-m-d")), array("data-date-format" => "DD.MM.YYYY")));
        //$this->addValidator('day_of_birth', new nomvcDateValidator(array('required' => false, 'in_format' => DateHelper::HTMLD_FORMAT)));


        //$this->addWidget(new nomvcInputTextWidget('Логин', 'login'));
        //$this->addValidator('login', new nomvcStringValidator(array('required' => true,'min' => 5, 'max' => 50)));

        $this->addWidget(new nomvcInputTextWidget('Learning ID', 'learning_id'));
        $this->addValidator('learning_id', new nomvcStringValidator(array('required' => true,'min' => 1, 'max' => 50)));
        
        $this->addWidget(new nomvcInputPasswordWidget('Пароль', 'passwd'));
        $this->addValidator('passwd', new nomvcStringValidator(array('required' => false, "min" => 6, "max" => 10)));

        $this->addWidget(new nomvcInputPasswordWidget('Подтвердите пароль', 'passwd_confirm'));
        $this->addValidator('passwd_confirm', new nomvcStringValidator(array('required' => false, "min" => 6, "max" => 10)));

        $this->addWidget(new nomvcInputTextWidget('Телефон', 'msisdn'));
        $this->addValidator('msisdn', new nomvcStringValidator(array('required' => false,'min' => 11, 'max' => 16)));

        $this->addWidget(new nomvcInputTextWidget('Email', 'email'));
        $this->addValidator('email', new nomvcStringValidator(array('required' => false, 'min' => 8, 'max' => 100)));

        $this->addWidget(new nomvcSelectFromMultipleDbWidget('Роль', 'id_role', array(
            'helper' => $this->context->getDbHelper(),
            'table' => 'V_ROLE',
            'order' => 'description',
            'val' => 'description'
        )));
        $this->addValidator('id_role', new nomvcValueInDbValidator(array('required' => true, 'helper' => $this->context->getDbHelper(), 'table' => 'V_ROLE', 'key' => 'id_role')));
        
        $this->addWidget(new nomvcSelectFromMultipleDbWidget('Статус', 'id_status', array(
            'helper' => $this->context->getDbHelper(),
            'table' => 'V_MEMBER_STATUS',
            'order' => 'name'
        )));
        $this->addValidator('id_status', new nomvcValueInDbValidator(array('required' => true, 'helper' => $this->context->getDbHelper(), 'table' => 'V_MEMBER_STATUS', 'key' => 'id_status')));
        
        //Регионы
        //$this->addWidget(new nomvcSelectFromMultipleDbWidget('Доступные регионы', 'regions', array(
        //    'helper' => $this->context->getDbHelper(), 'table' => 'V_REGION', 'key' => 'id_region'), array()));
        //$this->addValidator('regions', new nomvcArrayValidator(array('required' => false)));
    }

}
