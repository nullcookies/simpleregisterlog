<?php

class sfMoreSecure extends agAbstractComponent
{
    protected function init(){
        $this->time_limit_req = $this->context->getConfigVal('time_limit_req'); //second
        $this->token_life_time = $this->context->getConfigVal('token_life_time'); //minute

        $this->enable_limit_ip_req_per_time = $this->context->getConfigVal('enable_limit_ip_req_per_time');
        $this->limit_ip_req_per_time = $this->context->getConfigVal('limit_ip_req_per_time');

        $this->enable_limit_ip_uuid_req_per_time = $this->context->getConfigVal('enable_limit_ip_uuid_req_per_time');
        $this->limit_ip_uuid_req_per_time = $this->context->getConfigVal('limit_ip_uuid_req_per_time');

        $this->enable_limit_ip_uuid_msisdn_req_per_time = $this->context->getConfigVal('enable_limit_ip_uuid_msisdn_req_per_time');
        $this->limit_ip_uuid_msisdn_req_per_time = $this->context->getConfigVal('limit_ip_uuid_msisdn_req_per_time');


        $this->enable_limit_ip_token_req_per_time = $this->context->getConfigVal('enable_limit_ip_token_req_per_time');
        $this->limit_ip_token_req_per_time = $this->context->getConfigVal('limit_ip_token_req_per_time');

        $this->enable_limit_token_req_per_time = $this->context->getConfigVal('enable_limit_token_req_per_time');
        $this->limit_token_req_per_time = $this->context->getConfigVal('limit_token_req_per_time');


        $this->enable_check_token_expired = $this->context->getConfigVal('enable_check_token_expired');
        $this->enable_check_token_block = $this->context->getConfigVal('enable_check_token_block');
        $this->enable_check_token_invalid = $this->context->getConfigVal('enable_check_token_invalid');
    }

    //ограничение по кол-во запросов с ip в ед. времени
    private function check1(){
        $stmt = $this->context->getDb()->prepare('
        select count(*) as cnt
        from T_SECURITY_REQUEST_LOG 
        where NET = :NET
        and dt > dateadd(second, -'.$this->time_limit_req.', getdate())
        ');

        $stmt->bindValue('NET', $this->getIp());
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $cnt = $row['cnt'];
        $stmt->closeCursor();

        //var_dump($cnt); exit;
        if ($cnt > $this->limit_ip_req_per_time){
            throw new agGlobalException('limit requests from ip per time', agAbstractApiController::LIMIT_IP_REQ_PER_TIME);
        }
    }

    //ограничение по кол-во запросов с ip & uuid в ед. времени
    private function check2(){
        if (isset($this->request->params->uuid) && !empty($this->request->params->uuid)){
            $stmt = $this->context->getDb()->prepare('
            select count(*) as cnt
            from T_SECURITY_REQUEST_LOG 
            where NET = :NET
            and UUID = :UUID
            and dt > dateadd(second, -'.$this->time_limit_req.', getdate())
            ');

            $stmt->bindValue('NET', $this->getIp());
            $stmt->bindValue('UUID', $this->request->params->uuid);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $cnt = $row['cnt'];
            $stmt->closeCursor();

            //var_dump($cnt); exit;
            if ($cnt > $this->limit_ip_uuid_req_per_time){
                throw new agGlobalException('limit requests from ip and uuid per time', agAbstractApiController::LIMIT_IP_UUID_REQ_PER_TIME);
            }
        }
    }

    //ограничение по кол-во запросов с ip & uuid & msisdn в ед. времени
    private function check3(){
        if (isset($this->request->params->uuid) && !empty($this->request->params->uuid) && isset($this->request->params->msisdn) && !empty($this->request->params->msisdn)){
            $stmt = $this->context->getDb()->prepare('
            select count(*) as cnt
            from T_SECURITY_REQUEST_LOG 
            where NET = :NET
            and UUID = :UUID
            and MSISDN = :MSISDN
            and dt > dateadd(second, -'.$this->time_limit_req.', getdate())
            ');

            $stmt->bindValue('NET', $this->getIp());
            $stmt->bindValue('UUID', $this->request->params->uuid);
            $stmt->bindValue('MSISDN', $this->request->params->msisdn);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $cnt = $row['cnt'];
            $stmt->closeCursor();

            //var_dump($cnt); exit;
            if ($cnt > $this->limit_ip_uuid_msisdn_req_per_time){
                throw new agGlobalException('limit requests from ip and uuid and msisdn per time', agAbstractApiController::LIMIT_IP_UUID_MSISDN_REQ_PER_TIME);
            }
        }
    }

    //ограничение по кол-во запросов с ip & token в ед. времени
    private function check4(){
        if (isset($this->request->params->token) && !empty($this->request->params->token)){
            $stmt = $this->context->getDb()->prepare('
            select count(*) as cnt
            from T_SECURITY_REQUEST_LOG 
            where NET = :NET
            and TOKEN = :TOKEN
            and dt > dateadd(second, -'.$this->time_limit_req.', getdate())
            ');

            $stmt->bindValue('NET', $this->getIp());
            $stmt->bindValue('TOKEN', $this->request->params->token);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $cnt = $row['cnt'];
            $stmt->closeCursor();

            //var_dump($cnt); exit;
            if ($cnt > $this->limit_ip_token_req_per_time){
                throw new agGlobalException('limit requests from ip and token per time', agAbstractApiController::LIMIT_IP_TOKEN_REQ_PER_TIME);
            }
        }
    }

    //ограничение по кол-во запросов с token в ед.времени
    private function check5(){
        if (isset($this->request->params->token) && !empty($this->request->params->token)){
            $stmt = $this->context->getDb()->prepare('
            select count(*) as cnt
            from T_SECURITY_REQUEST_LOG 
            where TOKEN = :TOKEN
            and dt > dateadd(second, -'.$this->time_limit_req.', getdate())
            ');

            $stmt->bindValue('TOKEN', $this->request->params->token);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $cnt = $row['cnt'];
            $stmt->closeCursor();

            //var_dump($cnt); exit;
            if ($cnt > $this->limit_token_req_per_time){
                throw new agGlobalException('limit requests from token per time', agAbstractApiController::LIMIT_TOKEN_REQ_PER_TIME);
            }
        }
    }

    //проверка срока жизни токена
    private function check6(){
        if (isset($this->request->params->uuid) && !empty($this->request->params->uuid) && isset($this->request->params->token) && !empty($this->request->params->token) && isset($this->request->params->msisdn) && !empty($this->request->params->msisdn)){
            $stmt = $this->context->getDb()->prepare('
            select 
            count(*) as cnt_all,
            sum(case when dt < dateadd(minute, -'.$this->token_life_time.', getdate()) then 1 else 0 end) as cnt_expired
            from T_SECURITY_TOKEN_LOG
            where NET = :NET
            and UUID = :UUID
            and MSISDN = :MSISDN
            and TOKEN = :TOKEN
            and has_verify = 1
            ');

            $stmt->bindValue('NET', $this->getIp());
            $stmt->bindValue('UUID', $this->request->params->uuid);
            $stmt->bindValue('MSISDN', $this->request->params->msisdn);
            $stmt->bindValue('TOKEN', $this->request->params->token);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $cnt_all = $row['cnt_all'];
            $cnt_expired = $row['cnt_expired'];
            $stmt->closeCursor();


            if (in_array($this->request->action, array('get_prolongation_token'))){}
            elseif ($cnt_all > 0 && $cnt_expired > 0){
                throw new agGlobalException('token expired', agAbstractApiController::TOKEN_EXPIRED);
            }
        }
    }

    //проверка блокировки токена
    private function check7(){
        if (isset($this->request->params->uuid) && !empty($this->request->params->uuid) && isset($this->request->params->token) && !empty($this->request->params->token) && isset($this->request->params->msisdn) && !empty($this->request->params->msisdn)){
            $stmt = $this->context->getDb()->prepare('
            select 
            count(*) as cnt
            from T_SECURITY_TOKEN_LOG
            where NET = :NET
            and UUID = :UUID
            and MSISDN = :MSISDN
            and TOKEN = :TOKEN
            and IS_BLOCK = 1
            --and dt > dateadd(minute, -'.$this->token_life_time.', getdate())
            ');

            $stmt->bindValue('NET', $this->getIp());
            $stmt->bindValue('UUID', $this->request->params->uuid);
            $stmt->bindValue('MSISDN', $this->request->params->msisdn);
            $stmt->bindValue('TOKEN', $this->request->params->token);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $cnt = $row['cnt'];
            $stmt->closeCursor();

            //var_dump($row); exit;
            if ($cnt > 0){
                throw new agGlobalException('token blocked', agAbstractApiController::TOKEN_BLOCKED);
            }
        }
    }

    //проверка валидности токена
    private function check8(){
        if (isset($this->request->params->uuid) && !empty($this->request->params->uuid) && isset($this->request->params->token) && !empty($this->request->params->token) && isset($this->request->params->msisdn) && !empty($this->request->params->msisdn)){
            $stmt = $this->context->getDb()->prepare('
            select top 1 token, has_verify, uuid, net
            from T_SECURITY_TOKEN_LOG
            where MSISDN = :MSISDN
            --and UUID = :UUID
            --and NET = :NET
            and has_verify = 1
            order by DT DESC
            ');

            $stmt->bindValue('NET', $this->getIp());
            $stmt->bindValue('UUID', $this->request->params->uuid);
            $stmt->bindValue('MSISDN', $this->request->params->msisdn);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $last_token = isset($row['token'])?$row['token']:null;
            $has_verify = isset($row['has_verify'])?$row['has_verify']:0;
            $uuid = isset($row['uuid'])?$row['uuid']:null;
            $net = isset($row['net'])?$row['net']:null;
            $stmt->closeCursor();

            //проверка соответствия токена  = выданному владельцу 
            if ((($this->request->params->uuid != $uuid) || ($net != $this->getIp())) && !in_array($this->request->action, array('check_auth_code'))){
                throw new agGlobalException('invalid token', agAbstractApiController::INVALID_TOKEN);
            }
            
            if (in_array($this->request->action, array('check_auth_code', 'get_prolongation_token'))){
                //пост проверка введенного кода подтверждения
            }
            elseif ($last_token != $this->request->params->token || $has_verify != 1){
                throw new agGlobalException('invalid token', agAbstractApiController::INVALID_TOKEN);
            }
        }
    }

    public function checkRequest($request){
        $this->request = $request;

        if ($this->enable_limit_ip_req_per_time)
            $this->check1();

        if ($this->enable_limit_ip_uuid_req_per_time)
            $this->check2();

        if ($this->enable_limit_ip_uuid_msisdn_req_per_time)
            $this->check3();

        if ($this->enable_limit_ip_token_req_per_time)
            $this->check4();

        if ($this->enable_limit_token_req_per_time)
            $this->check5();


        if ($this->enable_check_token_invalid)
            $this->check8();
           
        if ($this->enable_check_token_block)
            $this->check7();

        if ($this->enable_check_token_expired)
            $this->check6();

        //инсертим в отдельный лог
        try {
            $stmt = $this->context->getDb()->prepare('insert into T_SECURITY_REQUEST_LOG(NET, UUID, MSISDN, TOKEN) VALUES(:NET, :UUID, :MSISDN, :TOKEN)');
            $stmt->bindValue('NET', $this->getIp());
            $stmt->bindValue('UUID', $this->request->params->uuid);
            $stmt->bindValue('MSISDN', $this->request->params->msisdn);
            $stmt->bindValue('TOKEN', $this->request->params->token);
            $stmt->execute();
            $stmt->closeCursor();
        }
        catch(exception $e){}
    }

    public static function crypto_rand_secure($min = 0, $max = 9) {
        $range = $max - $min;
        if ($range == 0) return $min; // not so random...
        $log = log($range, 2);
        $bytes = (int) ($log / 8) + 1; // length in bytes
        $bits = (int) $log + 1; // length in bits
        $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes, $s)));
            $rnd = $rnd & $filter; // discard irrelevant bits
        } while ($rnd >= $range);
        return $min + $rnd;
    }
}
