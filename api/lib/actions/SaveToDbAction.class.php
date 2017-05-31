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

        $this->dbHelper->addQuery($this->getAction().'/check_auto_email_notify', '
            select count(*) as cnt from T_SERVICE where id_service = :id_service and is_active = 1 and is_auto_email_notify = 1
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
        $has_service = $this->dbHelper->selectValue($this->getAction().'/check_exist_service',  array('id_service' => $this->getValue('id_service')));

        $has_auto_email_notify = $this->dbHelper->selectValue($this->getAction().'/check_auto_email_notify',  array('id_service' => $this->getValue('id_service')));

        if (!empty($has_service)) {
            $result = $this->dbHelper->execute($this->getAction().'/save_to_db_log',  array(
                'session_id' => session_id(),
                'net' => $this->getIp(),
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

            //var_dump($has_auto_email_notify); exit;
            if (!empty($has_auto_email_notify)) {
                $this->autoEmailNotify();
            }

            return array('result' => Errors::SUCCESS);
        }
        else
            $this->throwActionException(Errors::SERVICE_NOT_FOUND);

        return array('result' => Errors::FAIL);
    }

    public function autoEmailNotify(){
        try {
            $conn = $this->context->getDb();

            $sql = '
                select ts.email_subject_def as email_subject, ts.email_from_def as email_from 
                from T_SERVICE ts
                where ts.id_service = :id_service 
            ';
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':id_service', $this->getValue('id_service'), PDO::PARAM_INT);
            $stmt->execute();

            $prop = $stmt->fetch(PDO::FETCH_ASSOC);

            $sql = '
                select tsf.name as field, name_rus as label 
                from T_SHOW_FIELD tsf
            ';
            $stmt = $conn->prepare($sql);
            $stmt->execute();

            $fields = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $fields[$row['field']] = $row['label'];
            }
            //var_dump($fields); exit;

            $sql = '
                select T_SERVICE_EMAIL.id_service, email 
                from T_SERVICE_EMAIL
                inner join T_SERVICE on (T_SERVICE.ID_SERVICE = T_SERVICE_EMAIL.ID_SERVICE AND T_SERVICE.IS_ACTIVE = 1)
                where T_SERVICE.id_service = :id_service
                group by T_SERVICE_EMAIL.id_service, email
            ';
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':id_service', $this->getValue('id_service'), PDO::PARAM_INT);
            $stmt->execute();

            if (!empty($prop['email_subject'])){
                $subject = $prop['email_subject'];
            }
            else
                $subject = 'Получены новые данные';

            $subject = mb_convert_encoding($subject, 'cp1251');

            if (!empty($prop['email_from'])){
                $from = $prop['email_from'];
            }
            else
                $from = 'info@be-interactive.ru';

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $mail = new HtmlMimeMail();
                $body = '';

                foreach ($this->values as $key => $value) {
                    if (key_exists($key, $fields) && $value != null) {
                        $body .= "\n".$fields[$key] . ' : ' . $value;
                    }
                }
                //var_dump($body); exit;

                $mail->send(null, $row['email'], null, null, $from, $subject, $body);
            }
        }
        catch (exception $e){}

        return true;
    }

    public function getResponseExample() {
        return json_decode('{
  "response": {
    "result": 100
  }
}');
    }
}