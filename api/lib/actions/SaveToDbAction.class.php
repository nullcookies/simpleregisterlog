<?php

class SaveToDbAction extends AbstractAction {
    public function getTitle() {
        return 'Сохранить логи в БД';
    }

    public function init() {
        parent::init();

        $this->addParameter('id_service', new agStringValidator(array('required' => true)), 'ID Service');
        
        $this->addParameter('msisdn', new agMsisdnValidator(array('required' => false)), 'Номер телефона');

        $this->addParameter('email', new agEmailValidator(array('required' => false)), 'Email');
        
        $this->addParameter('name', new agStringValidator(array('required' => false)), 'Имя');

        $this->addParameter('surname', new agStringValidator(array('required' => false)), 'Фамилия');
        
        $this->addParameter('patronymic', new agStringValidator(array('required' => false)), 'Отчество');

        $this->addParameter('question_id', new agIntegerValidator(array('required' => false)), 'ID Вопроса');

        $this->addParameter('answer_id', new agIntegerValidator(array('required' => false)), 'ID ответа');

        $this->addParameter('answer_order_num', new agIntegerValidator(array('required' => false)), 'Порядковый номер ответа');

        $this->registerActionException(Errors::SERVICE_NOT_FOUND, 'Сервис не найден');

        $this->dbHelper->addQuery($this->getAction().'/check_exist_service', '
            select count(*) as cnt from T_SERVICE where id_service = :id_service and is_active = 1
        ');
        
        $this->dbHelper->addQuery($this->getAction().'/save_to_db_log', '
            insert into `T_LOG` (
                session_id,
                net,
                id_service, 
                name, 
                surname, 
                patronymic, 
                msisdn, 
                email,
                question_id,
                answer_id,
                answer_order_num
            ) values (
                :session_id,
                :net,
                :id_service, 
                :name, 
                :surname, 
                :patronymic, 
                :msisdn, 
                :email,
                :question_id,
                :answer_id,
                :answer_order_num
            )
        ');
    }

    public function execute() {
        $id_service = $this->dbHelper->selectValue($this->getAction().'/check_exist_service',  array('id_service' => $this->getValue('id_service')));
        
        if (!empty($id_service)) {            
            $result = $this->dbHelper->execute($this->getAction().'/save_to_db_log',  array(
                'session_id' => session_id(),
                'new' => $this->getIp(),
                'id_service' => $this->getValue('id_service'),
                'name' => $this->getValue('name'),
                'surname' => $this->getValue('surname'),
                'patronymic' => $this->getValue('patronymic'),
                'msisdn' => $this->getValue('msisdn'),
                'email' => $this->getValue('email'),
                'question_id' => $this->getValue('question_id'),
                'answer_id' => $this->getValue('answer_id'),
                'answer_order_num' => $this->getValue('answer_order_num')
            ));

            return array('result' => Errors::SUCCESS);
        }
        else
            $this->throwActionException(Errors::SERVICE_NOT_FOUND);

        return array('result' => Errors::FAIL);
    }

    public function getResponseExample() {
        return json_decode('{
  "response": {
    "result": 100
  }
}');
    }
}
