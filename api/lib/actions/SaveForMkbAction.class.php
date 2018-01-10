<?php

class SaveForMkbAction extends AbstractAction {

    //protected $dest_url = 'https://mkb.ru/api/request/consumercredit/wifiru?test=1';
    protected $dest_url = 'https://mkb.ru/api/request/consumercredit/wifiru';
    
    public function getTitle() {
        return 'Сохранить в стиле МКБ';
    }

    public function init() {
        parent::init();

        $this->addParameter('id_service', new agStringValidator(array('required' => true)), 'ID Service');

        $this->addParameter('LastName', new agStringValidator(array('required' => false)), 'LastName');
        
        $this->addParameter('FirstName', new agStringValidator(array('required' => false)), 'FirstName');
        
        $this->addParameter('MiddleName', new agStringValidator(array('required' => false)), 'MiddleName');
        
        $this->addParameter('BirthDay', new agStringValidator(array('required' => false)), 'BirthDay');
        
        $this->addParameter('Phone', new agStringValidator(array('required' => false)), 'Phone');
        
        $this->addParameter('Email', new agStringValidator(array('required' => false)), 'Email');
        
        $this->addParameter('Sum', new agStringValidator(array('required' => false)), 'Sum');
        
        $this->addParameter('MonthlyIncome', new agStringValidator(array('required' => false)), 'MonthlyIncome');
        
        $this->addParameter('LoanPeriod', new agStringValidator(array('required' => false)), 'LoanPeriod');
        
        $this->addParameter('Currency', new agStringValidator(array('required' => false)), 'Currency');
        
        $this->addParameter('MarketingAgreement', new agStringValidator(array('required' => false)), 'MarketingAgreement');
        
        $this->addParameter('DataAgreement', new agStringValidator(array('required' => false)), 'DataAgreement');
        
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
                answer_order_num,
                id_metro_line,
                id_metro_station,
                metro_station_order_num,
                company_type,
                no_of_staff,
                email_adress,
                inet_phone_spend_PM,
                data_bkup_spend_PM,
                srv_manage_cost_PM,
                city
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
                :answer_order_num,
                :id_metro_line,
                :id_metro_station,
                :metro_station_order_num,
                :company_type,
                :no_of_staff,
                :email_adress,
                :inet_phone_spend_PM,
                :data_bkup_spend_PM,
                :srv_manage_cost_PM,
                :city
            )
        ');
    }

    public function execute() {
        $has_service = $this->dbHelper->selectValue($this->getAction().'/check_exist_service',  array('id_service' => $this->getValue('id_service')));

        $has_auto_email_notify = $this->dbHelper->selectValue($this->getAction().'/check_auto_email_notify',  array('id_service' => $this->getValue('id_service')));

        if (!empty($has_service)) {
        
            $result = $this->sendToMkb();
            
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
			header('Content-Type: application/json');
			
            die($result);
            
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
                'answer_order_num' => $this->getValue('answer_order_num'),
                'id_metro_line' => $this->getValue('metro_line_id'),
                'id_metro_station' => $this->getValue('metro_station_id'),
                'metro_station_order_num' => $this->getValue('metro_station_order_num'),
                'company_type' => $this->getValue('company_type'),
                'no_of_staff' => $this->getValue('no_of_staff'),
                'email_adress' => $this->getValue('email_adress'),
                'inet_phone_spend_PM' => $this->getValue('inet_phone_spend_PM'),
                'data_bkup_spend_PM' => $this->getValue('data_bkup_spend_PM'),
                'srv_manage_cost_PM' => $this->getValue('srv_manage_cost_PM'),
                'city' => $this->getValue('city')
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
    
    protected function sendToMkb(){
        
        $post_data = array(
            'LastName' => $this->getValue('LastName'),
            'FirstName' => $this->getValue('FirstName'),
            'MiddleName' => $this->getValue('MiddleName'),
            'BirthDay' => $this->getValue('BirthDay'),
            'Phone' => $this->getValue('Phone'),
            'Email' => $this->getValue('Email'),
            'Sum' => $this->getValue('Sum'),
            'MonthlyIncome' => $this->getValue('MonthlyIncome'),
            'LoanPeriod' => $this->getValue('LoanPeriod'),
            'Currency' => $this->getValue('Currency'),
            'MarketingAgreement' => $this->getValue('MarketingAgreement'),
            'DataAgreement' => $this->getValue('DataAgreement')
        );
        
        
        //var_dump(http_build_query($post_data)); exit;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->dest_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('User-agent: php-curl sender', 'Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        curl_close($ch);
        
        return $server_output;
    }

    public function autoEmailNotify(){
        try {
            $conn = $this->context->getDb();

            $sql = '
                select ts.name, ts.email_subject_def as email_subject, ts.email_from_def as email_from 
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
                $subject = 'Поступила новая заявка на ('.$prop['name'].'). Дата поступления '.date('Y-m-d H:i:s');

            $subject = mb_convert_encoding($subject, 'cp1251');

            if (!empty($prop['email_from'])){
                $from = $prop['email_from'];
            }
            else
                $from = 'info@be-interactive.ru';

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $mail = new HtmlMimeMail();
                $body = "\n".$prop['name'];
                $body .= "\n";

                foreach ($this->values as $key => $value) {
                    if (key_exists($key, $fields) && $value != null) {
                        $body .= "\n".$fields[$key] . ' : ' . $value;
                    }
                }

                $body = mb_convert_encoding($body, 'cp1251');
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
