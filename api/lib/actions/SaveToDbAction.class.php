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

        $this->addParameter('metro_line_id', new agIntegerValidator(array('required' => false)), 'ID линии метро');

        $this->addParameter('metro_station_id', new agIntegerValidator(array('required' => false)), 'ID станции метро');

        $this->addParameter('metro_station_order_num', new agIntegerValidator(array('required' => false)), 'Порядковый номер станции');

        $this->registerActionException(Errors::SERVICE_NOT_FOUND, 'Сервис не найден');

        $this->dbHelper->addQuery($this->getAction().'/check_exist_service', '
            select count(*) as cnt from T_SERVICE where id_service = :id_service and is_active = 1
        ');

        $this->dbHelper->addQuery($this->getAction().'/check_auto_email_notify', '
            select count(*) as cnt from T_SERVICE where id_service = :id_service and is_active = 1 and is_auto_email_notify = 1
        ');

        $this->dbHelper->addQuery($this->getAction().'/select_station_line_and_by_order_num', '
            select *
            from
            (
                select 
                `ml`.`ID_METRO_LINE` AS `METRO_LINE_ID`,
                `ml`.`NAME` AS `METRO_LINE`,
                `tmt`.`ID_METRO_STATION` AS `METRO_STATION_ID`,
                `tmt`.`NAME` AS `METRO_STATION`,
                (@rownum1 := @rownum1 + 1) AS `METRO_STATION_ORDER_NUM`
                from `simpleregisterlog`.`T_METRO_LINE` `ml`
                join `simpleregisterlog`.`T_METRO_STATION` `tmt` on `ml`.`ID_METRO_LINE` = `tmt`.`ID_METRO_LINE` 
                join (SELECT @rownum1 := 0) r
                where `ml`.`ID_METRO_LINE` = 1
                
                union
                
                select 
                `ml`.`ID_METRO_LINE` AS `METRO_LINE_ID`,
                `ml`.`NAME` AS `METRO_LINE`,
                `tmt`.`ID_METRO_STATION` AS `METRO_STATION_ID`,
                `tmt`.`NAME` AS `METRO_STATION`,
                (@rownum2 := @rownum2 + 1) AS `METRO_STATION_ORDER_NUM`
                from `simpleregisterlog`.`T_METRO_LINE` `ml`
                join `simpleregisterlog`.`T_METRO_STATION` `tmt` on `ml`.`ID_METRO_LINE` = `tmt`.`ID_METRO_LINE` 
                join (SELECT @rownum2 := 0) r
                where `ml`.`ID_METRO_LINE` = 2
                
                union
                
                select 
                `ml`.`ID_METRO_LINE` AS `METRO_LINE_ID`,
                `ml`.`NAME` AS `METRO_LINE`,
                `tmt`.`ID_METRO_STATION` AS `METRO_STATION_ID`,
                `tmt`.`NAME` AS `METRO_STATION`,
                (@rownum3 := @rownum3 + 1) AS `METRO_STATION_ORDER_NUM`
                from `simpleregisterlog`.`T_METRO_LINE` `ml`
                join `simpleregisterlog`.`T_METRO_STATION` `tmt` on `ml`.`ID_METRO_LINE` = `tmt`.`ID_METRO_LINE` 
                join (SELECT @rownum3 := 0) r
                where `ml`.`ID_METRO_LINE` = 3
                
                union
                
                select 
                `ml`.`ID_METRO_LINE` AS `METRO_LINE_ID`,
                `ml`.`NAME` AS `METRO_LINE`,
                `tmt`.`ID_METRO_STATION` AS `METRO_STATION_ID`,
                `tmt`.`NAME` AS `METRO_STATION`,
                (@rownum4 := @rownum4 + 1) AS `METRO_STATION_ORDER_NUM`
                from `simpleregisterlog`.`T_METRO_LINE` `ml`
                join `simpleregisterlog`.`T_METRO_STATION` `tmt` on `ml`.`ID_METRO_LINE` = `tmt`.`ID_METRO_LINE` 
                join (SELECT @rownum4 := 0) r
                where `ml`.`ID_METRO_LINE` = 4
                
                union
                
                select 
                `ml`.`ID_METRO_LINE` AS `METRO_LINE_ID`,
                `ml`.`NAME` AS `METRO_LINE`,
                `tmt`.`ID_METRO_STATION` AS `METRO_STATION_ID`,
                `tmt`.`NAME` AS `METRO_STATION`,
                (@rownum5 := @rownum5 + 1) AS `METRO_STATION_ORDER_NUM`
                from `simpleregisterlog`.`T_METRO_LINE` `ml`
                join `simpleregisterlog`.`T_METRO_STATION` `tmt` on `ml`.`ID_METRO_LINE` = `tmt`.`ID_METRO_LINE` 
                join (SELECT @rownum5 := 0) r
                where `ml`.`ID_METRO_LINE` = 5
                
                union
                
                select 
                `ml`.`ID_METRO_LINE` AS `METRO_LINE_ID`,
                `ml`.`NAME` AS `METRO_LINE`,
                `tmt`.`ID_METRO_STATION` AS `METRO_STATION_ID`,
                `tmt`.`NAME` AS `METRO_STATION`,
                (@rownum6 := @rownum6 + 1) AS `METRO_STATION_ORDER_NUM`
                from `simpleregisterlog`.`T_METRO_LINE` `ml`
                join `simpleregisterlog`.`T_METRO_STATION` `tmt` on `ml`.`ID_METRO_LINE` = `tmt`.`ID_METRO_LINE` 
                join (SELECT @rownum6 := 0) r
                where `ml`.`ID_METRO_LINE` = 6
                
                union
                
                select 
                `ml`.`ID_METRO_LINE` AS `METRO_LINE_ID`,
                `ml`.`NAME` AS `METRO_LINE`,
                `tmt`.`ID_METRO_STATION` AS `METRO_STATION_ID`,
                `tmt`.`NAME` AS `METRO_STATION`,
                (@rownum7 := @rownum7 + 1) AS `METRO_STATION_ORDER_NUM`
                from `simpleregisterlog`.`T_METRO_LINE` `ml`
                join `simpleregisterlog`.`T_METRO_STATION` `tmt` on `ml`.`ID_METRO_LINE` = `tmt`.`ID_METRO_LINE` 
                join (SELECT @rownum7 := 0) r
                where `ml`.`ID_METRO_LINE` = 7
                
                union
                
                select 
                `ml`.`ID_METRO_LINE` AS `METRO_LINE_ID`,
                `ml`.`NAME` AS `METRO_LINE`,
                `tmt`.`ID_METRO_STATION` AS `METRO_STATION_ID`,
                `tmt`.`NAME` AS `METRO_STATION`,
                (@rownum8 := @rownum8 + 1) AS `METRO_STATION_ORDER_NUM`
                from `simpleregisterlog`.`T_METRO_LINE` `ml`
                join `simpleregisterlog`.`T_METRO_STATION` `tmt` on `ml`.`ID_METRO_LINE` = `tmt`.`ID_METRO_LINE` 
                join (SELECT @rownum8 := 0) r
                where `ml`.`ID_METRO_LINE` = 8
                
                union
                
                select 
                `ml`.`ID_METRO_LINE` AS `METRO_LINE_ID`,
                `ml`.`NAME` AS `METRO_LINE`,
                `tmt`.`ID_METRO_STATION` AS `METRO_STATION_ID`,
                `tmt`.`NAME` AS `METRO_STATION`,
                (@rownum9 := @rownum9 + 1) AS `METRO_STATION_ORDER_NUM`
                from `simpleregisterlog`.`T_METRO_LINE` `ml`
                join `simpleregisterlog`.`T_METRO_STATION` `tmt` on `ml`.`ID_METRO_LINE` = `tmt`.`ID_METRO_LINE` 
                join (SELECT @rownum9 := 0) r
                where `ml`.`ID_METRO_LINE` = 9
                
                union
                
                select 
                `ml`.`ID_METRO_LINE` AS `METRO_LINE_ID`,
                `ml`.`NAME` AS `METRO_LINE`,
                `tmt`.`ID_METRO_STATION` AS `METRO_STATION_ID`,
                `tmt`.`NAME` AS `METRO_STATION`,
                (@rownum10 := @rownum10 + 1) AS `METRO_STATION_ORDER_NUM`
                from `simpleregisterlog`.`T_METRO_LINE` `ml`
                join `simpleregisterlog`.`T_METRO_STATION` `tmt` on `ml`.`ID_METRO_LINE` = `tmt`.`ID_METRO_LINE` 
                join (SELECT @rownum10 := 0) r
                where `ml`.`ID_METRO_LINE` = 10
                
                union
                
                select 
                `ml`.`ID_METRO_LINE` AS `METRO_LINE_ID`,
                `ml`.`NAME` AS `METRO_LINE`,
                `tmt`.`ID_METRO_STATION` AS `METRO_STATION_ID`,
                `tmt`.`NAME` AS `METRO_STATION`,
                (@rownum11 := @rownum11 + 1) AS `METRO_STATION_ORDER_NUM`
                from `simpleregisterlog`.`T_METRO_LINE` `ml`
                join `simpleregisterlog`.`T_METRO_STATION` `tmt` on `ml`.`ID_METRO_LINE` = `tmt`.`ID_METRO_LINE` 
                join (SELECT @rownum11 := 0) r
                where `ml`.`ID_METRO_LINE` = 11
                
                union
                
                select 
                `ml`.`ID_METRO_LINE` AS `METRO_LINE_ID`,
                `ml`.`NAME` AS `METRO_LINE`,
                `tmt`.`ID_METRO_STATION` AS `METRO_STATION_ID`,
                `tmt`.`NAME` AS `METRO_STATION`,
                (@rownum12 := @rownum12 + 1) AS `METRO_STATION_ORDER_NUM`
                from `simpleregisterlog`.`T_METRO_LINE` `ml`
                join `simpleregisterlog`.`T_METRO_STATION` `tmt` on `ml`.`ID_METRO_LINE` = `tmt`.`ID_METRO_LINE` 
                join (SELECT @rownum12 := 0) r
                where `ml`.`ID_METRO_LINE` = 12
                
                order by `METRO_LINE_ID`, `METRO_STATION_ID`
            ) ts
            where metro_line_id = :id_metro_line
            and metro_station_order_num = :metro_station_order_num
            limit 1
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
                metro_station_order_num
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
                :metro_station_order_num
            )
        ');
    }

    public function execute() {
        $has_service = $this->dbHelper->selectValue($this->getAction().'/check_exist_service',  array('id_service' => $this->getValue('id_service')));

        $has_auto_email_notify = $this->dbHelper->selectValue($this->getAction().'/check_auto_email_notify',  array('id_service' => $this->getValue('id_service')));

        if (!empty($has_service)) {

            $row = $this->dbHelper->selectRow($this->getAction().'/select_station_line_and_by_order_num', array(
                'id_metro_line' => $this->getValue('metro_line_id'),
                'metro_station_order_num' => $this->getValue('metro_station_order_num')
            ));

            ///var_dump($row); exit;
            if (isset($row['metro_station_id']))
                $this->setValue('metro_station_id', $row['metro_station_id']);

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
                'metro_station_order_num' => $this->getValue('metro_station_order_num')
            ));

            //var_dump($has_auto_email_notify); exit;
            if (!empty($has_auto_email_notify)) {
                $this->autoEmailNotify();
            }

            if ($this->getValue('id_service') == 20){
                $top = $this->getTopStations();
                return array('result' => Errors::SUCCESS, 'data' => $top);
            }

            return array('result' => Errors::SUCCESS);
        }
        else
            $this->throwActionException(Errors::SERVICE_NOT_FOUND);

        return array('result' => Errors::FAIL);
    }

    protected function  getTopStationS()
    {
        $conn = $this->context->getDb();

        $sql = '
            SELECT *
            FROM (
                select
                ti.`METRO_LINE_ID`,
                ti.`METRO_LINE`,
                ti.`METRO_STATION_ID`,
                ti.`METRO_STATION`,
                count(*) as CNT
                from `T_LOG` tl
                inner join (
                    select 
                    `ml`.`ID_METRO_LINE` AS `METRO_LINE_ID`,
                    `ml`.`NAME` AS `METRO_LINE`,
                    `tmt`.`ID_METRO_STATION` AS `METRO_STATION_ID`,
                    `tmt`.`NAME` AS `METRO_STATION`,
                    (@rownum1 := @rownum1 + 1) AS `METRO_STATION_ORDER_NUM`
                    from `simpleregisterlog`.`T_METRO_LINE` `ml`
                    join `simpleregisterlog`.`T_METRO_STATION` `tmt` on `ml`.`ID_METRO_LINE` = `tmt`.`ID_METRO_LINE` 
                    join (SELECT @rownum1 := 0) r
                    where `ml`.`ID_METRO_LINE` = 1
            
                    union
            
                    select 
                    `ml`.`ID_METRO_LINE` AS `METRO_LINE_ID`,
                    `ml`.`NAME` AS `METRO_LINE`,
                    `tmt`.`ID_METRO_STATION` AS `METRO_STATION_ID`,
                    `tmt`.`NAME` AS `METRO_STATION`,
                    (@rownum2 := @rownum2 + 1) AS `METRO_STATION_ORDER_NUM`
                    from `simpleregisterlog`.`T_METRO_LINE` `ml`
                    join `simpleregisterlog`.`T_METRO_STATION` `tmt` on `ml`.`ID_METRO_LINE` = `tmt`.`ID_METRO_LINE` 
                    join (SELECT @rownum2 := 0) r
                    where `ml`.`ID_METRO_LINE` = 2
            
                    union
            
                    select 
                    `ml`.`ID_METRO_LINE` AS `METRO_LINE_ID`,
                    `ml`.`NAME` AS `METRO_LINE`,
                    `tmt`.`ID_METRO_STATION` AS `METRO_STATION_ID`,
                    `tmt`.`NAME` AS `METRO_STATION`,
                    (@rownum3 := @rownum3 + 1) AS `METRO_STATION_ORDER_NUM`
                    from `simpleregisterlog`.`T_METRO_LINE` `ml`
                    join `simpleregisterlog`.`T_METRO_STATION` `tmt` on `ml`.`ID_METRO_LINE` = `tmt`.`ID_METRO_LINE` 
                    join (SELECT @rownum3 := 0) r
                    where `ml`.`ID_METRO_LINE` = 3
            
                    union
            
                    select 
                    `ml`.`ID_METRO_LINE` AS `METRO_LINE_ID`,
                    `ml`.`NAME` AS `METRO_LINE`,
                    `tmt`.`ID_METRO_STATION` AS `METRO_STATION_ID`,
                    `tmt`.`NAME` AS `METRO_STATION`,
                    (@rownum4 := @rownum4 + 1) AS `METRO_STATION_ORDER_NUM`
                    from `simpleregisterlog`.`T_METRO_LINE` `ml`
                    join `simpleregisterlog`.`T_METRO_STATION` `tmt` on `ml`.`ID_METRO_LINE` = `tmt`.`ID_METRO_LINE` 
                    join (SELECT @rownum4 := 0) r
                    where `ml`.`ID_METRO_LINE` = 4
            
                    union
            
                    select 
                    `ml`.`ID_METRO_LINE` AS `METRO_LINE_ID`,
                    `ml`.`NAME` AS `METRO_LINE`,
                    `tmt`.`ID_METRO_STATION` AS `METRO_STATION_ID`,
                    `tmt`.`NAME` AS `METRO_STATION`,
                    (@rownum5 := @rownum5 + 1) AS `METRO_STATION_ORDER_NUM`
                    from `simpleregisterlog`.`T_METRO_LINE` `ml`
                    join `simpleregisterlog`.`T_METRO_STATION` `tmt` on `ml`.`ID_METRO_LINE` = `tmt`.`ID_METRO_LINE` 
                    join (SELECT @rownum5 := 0) r
                    where `ml`.`ID_METRO_LINE` = 5
            
                    union
            
                    select 
                    `ml`.`ID_METRO_LINE` AS `METRO_LINE_ID`,
                    `ml`.`NAME` AS `METRO_LINE`,
                    `tmt`.`ID_METRO_STATION` AS `METRO_STATION_ID`,
                    `tmt`.`NAME` AS `METRO_STATION`,
                    (@rownum6 := @rownum6 + 1) AS `METRO_STATION_ORDER_NUM`
                    from `simpleregisterlog`.`T_METRO_LINE` `ml`
                    join `simpleregisterlog`.`T_METRO_STATION` `tmt` on `ml`.`ID_METRO_LINE` = `tmt`.`ID_METRO_LINE` 
                    join (SELECT @rownum6 := 0) r
                    where `ml`.`ID_METRO_LINE` = 6
            
                    union
            
                    select 
                    `ml`.`ID_METRO_LINE` AS `METRO_LINE_ID`,
                    `ml`.`NAME` AS `METRO_LINE`,
                    `tmt`.`ID_METRO_STATION` AS `METRO_STATION_ID`,
                    `tmt`.`NAME` AS `METRO_STATION`,
                    (@rownum7 := @rownum7 + 1) AS `METRO_STATION_ORDER_NUM`
                    from `simpleregisterlog`.`T_METRO_LINE` `ml`
                    join `simpleregisterlog`.`T_METRO_STATION` `tmt` on `ml`.`ID_METRO_LINE` = `tmt`.`ID_METRO_LINE` 
                    join (SELECT @rownum7 := 0) r
                    where `ml`.`ID_METRO_LINE` = 7
            
                    union
            
                    select 
                    `ml`.`ID_METRO_LINE` AS `METRO_LINE_ID`,
                    `ml`.`NAME` AS `METRO_LINE`,
                    `tmt`.`ID_METRO_STATION` AS `METRO_STATION_ID`,
                    `tmt`.`NAME` AS `METRO_STATION`,
                    (@rownum8 := @rownum8 + 1) AS `METRO_STATION_ORDER_NUM`
                    from `simpleregisterlog`.`T_METRO_LINE` `ml`
                    join `simpleregisterlog`.`T_METRO_STATION` `tmt` on `ml`.`ID_METRO_LINE` = `tmt`.`ID_METRO_LINE` 
                    join (SELECT @rownum8 := 0) r
                    where `ml`.`ID_METRO_LINE` = 8
            
                    union
            
                    select 
                    `ml`.`ID_METRO_LINE` AS `METRO_LINE_ID`,
                    `ml`.`NAME` AS `METRO_LINE`,
                    `tmt`.`ID_METRO_STATION` AS `METRO_STATION_ID`,
                    `tmt`.`NAME` AS `METRO_STATION`,
                    (@rownum9 := @rownum9 + 1) AS `METRO_STATION_ORDER_NUM`
                    from `simpleregisterlog`.`T_METRO_LINE` `ml`
                    join `simpleregisterlog`.`T_METRO_STATION` `tmt` on `ml`.`ID_METRO_LINE` = `tmt`.`ID_METRO_LINE` 
                    join (SELECT @rownum9 := 0) r
                    where `ml`.`ID_METRO_LINE` = 9
            
                    union
            
                    select 
                    `ml`.`ID_METRO_LINE` AS `METRO_LINE_ID`,
                    `ml`.`NAME` AS `METRO_LINE`,
                    `tmt`.`ID_METRO_STATION` AS `METRO_STATION_ID`,
                    `tmt`.`NAME` AS `METRO_STATION`,
                    (@rownum10 := @rownum10 + 1) AS `METRO_STATION_ORDER_NUM`
                    from `simpleregisterlog`.`T_METRO_LINE` `ml`
                    join `simpleregisterlog`.`T_METRO_STATION` `tmt` on `ml`.`ID_METRO_LINE` = `tmt`.`ID_METRO_LINE` 
                    join (SELECT @rownum10 := 0) r
                    where `ml`.`ID_METRO_LINE` = 10
            
                    union
            
                    select 
                    `ml`.`ID_METRO_LINE` AS `METRO_LINE_ID`,
                    `ml`.`NAME` AS `METRO_LINE`,
                    `tmt`.`ID_METRO_STATION` AS `METRO_STATION_ID`,
                    `tmt`.`NAME` AS `METRO_STATION`,
                    (@rownum11 := @rownum11 + 1) AS `METRO_STATION_ORDER_NUM`
                    from `simpleregisterlog`.`T_METRO_LINE` `ml`
                    join `simpleregisterlog`.`T_METRO_STATION` `tmt` on `ml`.`ID_METRO_LINE` = `tmt`.`ID_METRO_LINE` 
                    join (SELECT @rownum11 := 0) r
                    where `ml`.`ID_METRO_LINE` = 11
            
                    union
            
                    select 
                    `ml`.`ID_METRO_LINE` AS `METRO_LINE_ID`,
                    `ml`.`NAME` AS `METRO_LINE`,
                    `tmt`.`ID_METRO_STATION` AS `METRO_STATION_ID`,
                    `tmt`.`NAME` AS `METRO_STATION`,
                    (@rownum12 := @rownum12 + 1) AS `METRO_STATION_ORDER_NUM`
                    from `simpleregisterlog`.`T_METRO_LINE` `ml`
                    join `simpleregisterlog`.`T_METRO_STATION` `tmt` on `ml`.`ID_METRO_LINE` = `tmt`.`ID_METRO_LINE` 
                    join (SELECT @rownum12 := 0) r
                    where `ml`.`ID_METRO_LINE` = 12
            
                    order by `METRO_LINE_ID`, `METRO_STATION_ID`
                ) ti on tl.`id_metro_line` = ti.`METRO_LINE_ID` and tl.`id_metro_station` = ti.`metro_station_id`
                where tl.`id_service` = :id_service
                group by 
                ti.`METRO_LINE_ID`,
                ti.`METRO_LINE`,
                ti.`METRO_STATION_ID`,
                ti.`METRO_STATION`  
            ) t0
            limit 10
            ';
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':id_service', $this->getValue('id_service'), PDO::PARAM_INT);
        $stmt->execute();

        $data = array();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $data[] = $row;
        }

        return $data;
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