<?php

abstract class AbstractAction extends agAbstractAction {
    
    protected $dbHelper;

    protected $code_time_remained;
    protected $code_lifetime;

    protected $web_path;
    protected $media_image_path;

    protected $transferredParams;

    protected $emails_super_admin = ['max-well98@mail.ru'];
    protected $email_from = 'info@ias.su';
    
    public function getAction() {
        return preg_replace('/_action$/imu', '', agAbstractController::fromCamelCase(get_class($this)));
    }
    
    public function getAccessRoles() {
        return array('client');
    }
    
    public function makeUrlForImageDb($objType, $id_file){
        return $this->url_image.'obj='.$objType.'&id='.$id_file;
    }

    public function getIp() {
        $ipaddress = '';

        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];

        $ipaddress = ip2long($ipaddress);

        return $ipaddress;
    }


    public function init() {
        $this->dbHelper = $this->context->getDbHelper();

        $this->dbHelper->addQuery($this->getAction().'/get_member', '
                SELECT *
                from V_MEMBER
                where login = :msisdn
        ');

        $this->dbHelper->addQuery($this->getAction().'/save_to_db_log_new', '
            insert into T_LOG (
                session_id,
                net,
                id_service
            ) values (
                :session_id,
                :net,
                :id_service
            )
        ');

        $this->dbHelper->addQuery($this->getAction().'/get_meta_key', '
            select *
            from `T_META_KEY`
            where lower(name) = lower(:name)
            limit 1
        ');

        $this->dbHelper->addQuery($this->getAction().'/save_meta_int', '
            insert into T_LOG_META_INT (
                id_log,
                id_meta_key,
                meta_value
            ) values (
                :id_log,
                :id_meta_key,
                :meta_value
            )
        ');

        $this->dbHelper->addQuery($this->getAction().'/save_meta_time', '
            insert into T_LOG_META_TIME (
                id_log,
                id_meta_key,
                meta_value
            ) values (
                :id_log,
                :id_meta_key,
                :meta_value
            )
        ');

        $this->dbHelper->addQuery($this->getAction().'/save_meta_text', '
            insert into T_LOG_META_TEXT (
                id_log,
                id_meta_key,
                meta_value
            ) values (
                :id_log,
                :id_meta_key,
                :meta_value
            )
        ');

       // $this->url_image = 'http://'.$_SERVER['HTTP_HOST'].'/api/img.php?';
//        $this->url_image_preview = 'http://'.$_SERVER['HTTP_HOST'].'/images/preview/';

//        $this->url_video = 'http://'.$_SERVER['HTTP_HOST'].'/videos/';
//        $this->url_video_preview = 'http://'.$_SERVER['HTTP_HOST'].'/videos/preview/';

//        $this->url_file = 'http://'.$_SERVER['HTTP_HOST'].'/files/';
//        $this->url_file_preview = 'http://'.$_SERVER['HTTP_HOST'].'/files/preview/';

//        $this->path_image = DIRNAME(__FILE__).'/../../images/';
//        $this->path_image_preview = DIRNAME(__FILE__).'/../../images/preview/';

//        $this->path_video = DIRNAME(__FILE__).'/../../videos/';
//        $this->path_video_preview = DIRNAME(__FILE__).'/../../videos/preview/';
//
//        $this->path_file = DIRNAME(__FILE__).'/../../files/';
//        $this->path_file_preview = DIRNAME(__FILE__).'/../../files/preview/';
//        
        
//        $this->code_time_remained = $this->context->getConfigVal('code_time_remained');
//        $this->code_lifetime = $this->context->getConfigVal('code_lifetime');
//
//        $this->dbHelper->addQuery($this->getAction().'/set_context_param', 'begin project_context.set_parameter(:param, :val); end;');
//
//        $this->dbHelper->addQuery($this->getAction().'/get_from_all_member', '
//        select
//        tm.id_member as id,
//        tm.surname,
//        tm.name,
//        tm.patronymic,
//        tm.msisdn,
//        tm.email,
//        to_char(tm.dt, \'YYYY-MM-DD HH24:MI:SS\') as dt_create,
//        tm.id_sex,
//        to_char(tm.dt_birthday, \'YYYY-MM-DD HH24:MI:SS\') as dt_birthday,
//        vs.name as sex
//        from T_MEMBER tm
//        left join V_SEX vs on tm.id_sex = vs.id_sex
//        where tm.msisdn = :msisdn
//        and tm.id_status = 1
//        ');
//
//        $this->dbHelper->addQuery($this->getAction().'/get_not_verify_member', '
//        select
//        tm.id_member as id,
//        tm.surname,
//        tm.name,
//        tm.patronymic,
//        tm.msisdn,
//        tm.email,
//        to_char(tm.dt, \'YYYY-MM-DD HH24:MI:SS\') as dt_create,
//        tm.id_sex,
//        to_char(tm.dt_birthday, \'YYYY-MM-DD HH24:MI:SS\') as dt_birthday,
//        vs.name as sex
//        from T_MEMBER tm
//        left join V_SEX vs on tm.id_sex = vs.id_sex
//        where tm.msisdn = :msisdn
//        --and tm.uuid = :uuid
//        and tm.has_verify = 0
//        and tm.id_status = 1	
//        ');
//
//        $this->dbHelper->addQuery($this->getAction().'/get_verify_member', '
//        select
//        tm.id_member as id,
//        tm.surname,
//        tm.name,
//        tm.patronymic,
//        tm.msisdn,
//        tm.email,
//        to_char(tm.dt, \'YYYY-MM-DD HH24:MI:SS\') as dt_create,
//        tm.id_sex,
//        to_char(tm.dt_birthday, \'YYYY-MM-DD HH24:MI:SS\') as dt_birthday,
//        vs.name as sex
//        from T_MEMBER tm
//        left join V_SEX vs on tm.id_sex = vs.id_sex
//        where tm.msisdn = :msisdn
//        --and tm.uuid = :uuid
//        and tm.has_verify = 1
//        and tm.id_status = 1
//        --and tm.email is not null
//        ');
//
//        $this->dbHelper->addQuery($this->getAction().'/get_member_info_ext', '
//        select
//        tm.id_member as id,
//        tm.surname,
//        tm.name,
//        tm.patronymic,
//        tm.msisdn,
//        tm.email,
//        to_char(tm.dt, \'YYYY-MM-DD HH24:MI:SS\') as dt_create,
//        tm.id_sex,
//        to_char(tm.dt_birthday, \'YYYY-MM-DD HH24:MI:SS\') as dt_birthday,
//        vs.name as sex,
//        t0.id_member_photo
//        from T_MEMBER tm
//        left join (
//            with sel as (
//                select 
//                ROW_NUMBER() OVER (PARTITION BY tmp.id_member ORDER BY tmp.id_member_photo desc) as rn,
//                tmp.id_member_photo,
//                tmp.id_member,
//                tmp.mime_type,
//                tmp.file_bin,
//                tmp.is_preview
//                from T_MEMBER_PHOTO tmp
//                where tmp.id_member = :id_member
//                order by tmp.id_member_photo, rn asc
//            )
//            select
//            sel.id_member_photo,
//            sel.id_member,
//            sel.mime_type,
//            sel.file_bin
//            from sel 
//            where rn = 1
//        ) t0 on t0.id_member = tm.id_member
//        left join V_SEX vs on tm.id_sex = vs.id_sex
//        where tm.id_member = :id_member
//        --and tm.uuid = :uuid
//        and tm.has_verify = 1
//        and tm.id_status = 1
//        ');
//
//        $this->dbHelper->addQuery($this->getAction().'/save_member_role', '
//          insert into t_member_role (id_member, id_role) values(:id_member, :id_role)
//        ');
//        
//        $this->dbHelper->addQuery($this->getAction().'/check_password', "
//        select count(*) as cnt
//        from 
//        (
//            select *
//            from (
//                select password 
//                from T_SMS_OUT 
//                where id_member = :id_member
//                --and uuid = :uuid
//                and has_verify != 1
//                and dt_send > sysdate - (:code_lifetime/24/60/60)
//                order by dt_send desc
//            )
//            where rownum = 1 
//        ) t
//        where t.password = :password
//        ");
//
//        $this->dbHelper->addQuery($this->getAction().'/get_cnt_last_code', "
//        select 
//        count(*) as cnt,
//        ABS((max(dt_send) - sysdate)*24*60*60) as code_time_remained
//        from T_SMS_OUT 
//        where msisdn = :msisdn
//        --and uuid = :uuid
//--        and has_verify != 1
//        and dt_send > sysdate - (:code_time_remained/24/60/60)
//        ");
//
//
//        $this->dbHelper->addQuery($this->getAction().'/set_verify_code', "
//        update T_SMS_OUT 
//        set 
//        has_verify = 1, 
//        dt_verify = sysdate 
//        where id_member = :id_member 
//        and password = :code 
//        and has_verify != 1
//        ");
//
//        $this->dbHelper->addQuery($this->getAction().'/get_member_info_by_msisdn','
//        select
//        tm.id_member as id,
//        tm.surname,
//        tm.name,
//        tm.patronymic,
//        tm.msisdn,
//        tm.email,
//        to_char(tm.dt, \'YYYY-MM-DD HH24:MI:SS\') as dt_create,
//        tm.id_sex,
//        to_char(tm.dt_birthday, \'YYYY-MM-DD HH24:MI:SS\') as dt_birthday,
//        vs.name as sex
//        from T_MEMBER tm
//        left join V_SEX vs on tm.id_sex = vs.id_sex
//        where tm.msisdn = :msisdn
//        ');
//
//        $this->dbHelper->addQuery($this->getAction().'/update_member_registration', "
//        update T_MEMBER 
//        set uuid = :uuid, 
//        id_device = nvl(:id_device, id_device), 
//        has_push = :has_push, 
//        os = :os, 
//        has_verify = :has_verify, 
//        dt_verify = TO_DATE(:dt_verify, 'YYYY-MM-DD HH24:MI:SS') 
//        where id_member = :id_member 
//        and has_verify != 1");
//        
//        $this->dbHelper->addQuery($this->getAction().'/auth', '
//        select
//        tm.id_member as id,
//        tm.surname,
//        tm.name,
//        tm.patronymic,
//        tm.msisdn,
//        tm.email,
//        to_char(tm.dt, \'YYYY-MM-DD HH24:MI:SS\') as dt_create,
//        tm.id_sex,
//        to_char(tm.dt_birthday, \'YYYY-MM-DD HH24:MI:SS\') as dt_birthday,
//        vs.name as sex
//        from T_MEMBER tm
//        left join V_SEX vs on tm.id_sex = vs.id_sex
//        where tm.msisdn = :msisdn
//        --and tm.uuid = :uuid
//        and tm.passwd = :password
//        and tm.has_verify = 1
//        and tm.id_status = 1
//        ');
//        
//        $this->dbHelper->addQuery($this->getAction().'/save_member', '
//        insert into t_member (
//            uuid, 
//            surname, 
//            name, 
//            patronymic, 
//            msisdn, 
//            email, 
//            --passwd, 
//            os,
//            id_device
//        ) 
//        values(
//            :uuid, 
//            :surname, 
//            :name, 
//            :patronymic, 
//            :msisdn, 
//            :email, 
//            --:password, 
//            :os,
//            :id_device
//        ) returning id_member into :id_member');
//
//        $this->dbHelper->addQuery($this->getAction().'/update_member_password', '
//        update t_member 
//        set passwd = nvl(:passwd, passwd), 
//        dt_verify = decode(:has_verify, 1, sysdate, 0, null, dt_verify), 
//        has_verify = nvl(:has_verify, has_verify)
//        where id_member = :id_member
//        ');
//
//        $this->dbHelper->addQuery($this->getAction().'/save_car', '
//        insert into t_member_car (
//            id_member,
//            car_number, 
//            car_code, 
//            car_brand
//        ) 
//        values(
//            :id_member, 
//            :car_number, 
//            :car_code, 
//            :car_brand
//        )');
//
//        $this->dbHelper->addQuery($this->getAction().'/clear_device_info', 'update T_MEMBER set id_device = null where id_device = :id_device and id_member != :id_member');
//
//        $this->dbHelper->addQuery($this->getAction().'/update_device_info', 'update T_MEMBER set id_device = :id_device, has_push = :has_push where id_member = :id_member');
//        
//        $this->addParameter('uuid', new agStringValidator(array('required' => true)), 'Универсальный идентификатор устройства');
//        
        /************************ERRORS**********/

        $this->registerActionException(Errors::FAIL, 'Ошибка');

//        $this->registerActionException(Errors::NO_DATA_FOUND, 'Данные не найдены');
  //      $this->registerActionException(Errors::MEMBER_NOT_FOUND, 'Пользователь не найден');

    }
    
    protected function RemoveNullNode($array){
//        foreach ($array as $key => $val){
//            if ($val == null)
//                unset($array[$key]);
//        }
        return $array;
    }

    protected function saveToLog($id_service){
        $this->dbHelper->execute($this->getAction().'/save_to_db_log_new', array(
            'session_id' => session_id(),
            'net' => $this->getIp(),
            'id_service' => $id_service
        ));

        return $this->context->getDb()->lastInsertid();
    }

    protected function saveMetaValue($id_log, $meta_name, $meta_value){
        try {
            if ($meta = $this->dbHelper->selectRow($this->getAction() . '/get_meta_key', array('name' => $meta_name))) {
                $meta = $this->asStrictTypes($meta);
                foreach ($meta as $key => $val) {
                    $meta[strtolower($key)] = strtolower($val);
                }

                switch ($meta['meta_type']) {
                    case 'int':
                        $this->dbHelper->execute($this->getAction() . '/save_meta_int', array(
                            'id_log' => $id_log,
                            'id_meta_key' => $meta['id_meta_key'],
                            'meta_value' => $meta_value
                        ));
                        break;
                    case 'time':
                        $this->dbHelper->execute($this->getAction() . '/save_meta_time', array(
                            'id_log' => $id_log,
                            'id_meta_key' => $meta['id_meta_key'],
                            'meta_value' => $meta_value
                        ));
                        break;
                    case 'text':
                    default:
                        $this->dbHelper->execute($this->getAction() . '/save_meta_text', array(
                            'id_log' => $id_log,
                            'id_meta_key' => $meta['id_meta_key'],
                            'meta_value' => $meta_value
                        ));
                        break;
                }

                return true;
            }
        }
        catch(exception $e){}

        return false;
    }

    protected function checkLimitSendCode($id_member){
        return $this->dbHelper->execute($this->getAction().'/check_limit_send_code', array(
            'id_member' => $id_member,
            'code_time_remained' => $this->code_time_remained
        ));
    }
    
    public function saveMemberCar($id_member){
        if (!empty($this->getValue('car_number') || !empty($this->getValue('car_code')))){
            try {
                $this->dbHelper->execute($this->getAction() . '/save_car', array(
                    'id_member' => $id_member,
                    'car_number' => $this->getValue('car_number'),
                    'car_code' => $this->getValue('car_code'),
                    'car_brand' => $this->getValue('car_brand'),
                    'os' => $this->context->getUser()->getAttribute('os')
                ));
            } catch (exception $e) {}
        }
    }
    
    public function generatePassword(){
        return substr(sha3($this->crypto_rand_secure(1111111111, 9999999999)), 0, 6);
    }

    protected function sendPassword($id_member, $msisdn, $password, $is_restore = false){
        //предпроверка
        $row = $this->dbHelper->selectRow($this->getAction().'/get_cnt_last_code', array('msisdn' => $msisdn, 'code_time_remained' => $this->code_time_remained));

        if (isset($row['cnt']) && $row['cnt'] > 0){
            $this->code_time_remained = $this->code_time_remained - $row['code_time_remained'];
            $this->throwActionException(Errors::CODE_TIME_LIMIT);
        }
        
        $message = "Ваш пароль для входа: $password";

        $stmt = $this->context->getDb()->prepare('begin send_sms(:id_member, :msisdn, :message, :password, :is_restore); end;');
        $stmt->bindValue('id_member', $id_member);
        $stmt->bindValue('msisdn', $msisdn);
        $stmt->bindValue('message', $message);
        $stmt->bindValue('password', $password);
        $stmt->bindValue('is_restore', ($is_restore==true?1:0));
        
        return $stmt->execute();
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
    
    public function updateMemberPassword($id_member, $password, $has_verify = false){
        return $this->dbHelper->execute($this->getAction().'/update_member_password', array(
                'id_member' => $id_member,
                'passwd' => $password,
                'has_verify' => $has_verify
            ));
    }
    
    public function saveMember(){
        try {
            $id_member = null;

            //save member
            $r = $this->dbHelper->execute($this->getAction().'/save_member', array(
                'uuid' => $this->getValue('uuid'),
                'name' => $this->getValue('name'),
                'surname' => $this->getValue('surname'),
                'patronymic' => $this->getValue('patronymic'),
                'msisdn' => $this->getValue('msisdn'),
                'email' => $this->getValue('email'),
                'os' => $this->context->getUser()->getAttribute('os'),
                'id_device' => $this->getValue('id_device')
                //'password' => $password
            ), array('id_member' => &$id_member));
            
            if ($id_member){
                $this->dbHelper->execute($this->getAction().'/save_member_role', array(
                    'id_member' => $id_member,
                    'id_role' => 3    
                ));
                    
                $this->saveMemberCar($id_member);

                $password = $this->generatePassword();
                if ($this->sendPassword($id_member, $this->getValue('msisdn'), $password))
                    $this->updateMemberPassword($id_member, $password);
            }
        }
        catch(exception $e){}
        
        return $this->getMemberInfoByMsisdn($this->getValue('msisdn'));
    }

    protected function saveMemberDevice($id_member, $id_device = false, $has_push = 'Y'){
        //удаляем если такие имеются
        $this->dbHelper->execute($this->getAction().'/delete_device',array('id_device' => $id_device?$id_device:$this->getValue('id_device')));

        //добавляем по новой
        $this->dbHelper->execute($this->getAction().'/save_member_device',array('id_member' => $id_member, 'uuid' => $this->getValue('uuid'),'id_device' => $id_device?$id_device:$this->getValue('id_device'), 'has_push' => $has_push =='Y'?1:0, 'os' => $this->context->getUser()->getAttribute('os')));
    }
    

    protected function getFromAllMember(){
        $row = $this->dbHelper->selectRow($this->getAction().'/get_from_all_member',  array('msisdn' => $this->getValue('msisdn')));
        $row = $this->asStrictTypes($row);
        return $row;
    }

    public function auth(){
        $member = $this->dbHelper->selectRow($this->getAction().'/auth', array(
            'msisdn'=>$this->getValue('msisdn'), 
            'uuid' => $this->getValue('uuid'),
            'password' => $this->getValue('password')
        ));
        
        if (empty($member['id'])){
            $this->throwActionException(Errors::MEMBER_NOT_FOUND);
        }
        else {
            return $this->getMemberInfoByMsisdn($this->getValue('msisdn'));
        }
    }

    protected function getMember(){
        $member = $this->dbHelper->selectRow($this->getAction().'/get_member',  array('msisdn' => $this->getValue('msisdn')));
        return $member;
    }

    protected function getMemberInfoExt($id_member){
        $info = $this->dbHelper->selectRow($this->getAction().'/get_member_info_ext', array(
            'id_member' => $id_member
        ));

        if (!empty($info)){
            if (isset($info['id_member_photo']) && !empty($info['id_member_photo'])){
                $info['photo_path'] = $this->makeUrlForImageDb('member', $info['id_member_photo']);
            }
            unset($info['id_member_photo']);
        }
        
        //balance
        
        //
        
        return $info;
    }

    protected function getNotVerifyMember(){
        return $this->dbHelper->selectRow($this->getAction().'/get_not_verify_member',  array('msisdn' => $this->getValue('msisdn'), 'uuid' => $this->getValue('uuid')));
    }

    public function getMemberInfoByMsisdn($msisdn){
        if ($row = $this->dbHelper->selectRow($this->getAction().'/get_member_info_by_msisdn', array(
                'msisdn'=>$msisdn?$msisdn:$this->getValue('msisdn'))
        )){
            $row = $this->asStrictTypes($row, array('email' => 'string'));
            return $row;
        }
        //else
        //    $this->throwActionException(Errors::NO_DATA_FOUND);
    }
    
    public function checkcode($id_member, $code){
        $cnt = $this->dbHelper->selectValue($this->getAction().'/check_password',  array('id_member' => $id_member, 'uuid' => $this->getValue('uuid'), 'password' => $code, 'code_lifetime' => $this->code_lifetime));
        
        //помечаем как подтвержденный
        if ($cnt > 0){
            //помечаем лог
            if ($this->setverifycode($id_member, $code)){
                //для пользователя
                $this->updateMemberPassword($id_member, $code, true);
                return true;
            }
        }

        return false;
    }

    protected function updateMemberRegistration($id_member, $id_device = false, $has_push = 'Y', $has_verify = 'N'){
        $id_device = $id_device?$id_device:$this->getValue('id_device');

        //удаляем девайс, если у кого уже такой зарегистрирован
        $this->clearDeviceInfo($id_member, $id_device);

        return $this->dbHelper->execute($this->getAction().'/update_member_registration',array('id_member' => $id_member, 'uuid' => $this->getValue('uuid'), 'id_device' => $id_device, 'has_push' => $has_push =='Y'?1:0, 'os' => $this->context->getUser()->getAttribute('os'), 'has_verify' => $has_verify =='Y'?1:0, 'dt_verify' => $has_verify =='Y'?date('Y-m-d H:i:s'):null));
    }

    protected function clearDeviceInfo($id_member, $id_device){
        return $this->dbHelper->execute($this->getAction().'/clear_device_info', array('id_member' => $id_member, 'id_device' => $id_device));
    }

    public function updateDeviceInfo($id_member, $id_device, $has_push = true){
        return $this->dbHelper->execute($this->getAction().'/update_device_info', array('id_member' => $id_member, 'id_device' => $id_device, 'has_push' => $has_push ==true?1:0));
    }
    
    public final function getGroupsDescription() {
        return array(
            'service'	=> 'Для сервисного приложения',
            'client'	=> 'Для клиентского приложения'
        );
    }

    public function setverifycode($id_member, $code){
        $this->dbHelper->execute($this->getAction().'/set_verify_code',  array('id_member' => $id_member, 'code' => $code));
        return true;
    }

    private function RandomString(){
        $characters ='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randstring = '';
        for ($i = 0; $i < 10; $i++) {
            $randstring = $characters[sfMoreSecure::crypto_rand_secure(0, strlen($characters)-1)];
        }
        return $randstring;
    }

    public function generateFileName($ext){
        return sha3($this->RandomString().sfMoreSecure::crypto_rand_secure(111111111111, 999999999999999)).$this->getExtensionFromType($ext);
    }	

    public function base64_to_image($base64_string, $output_file) {
        $ifp = fopen($output_file, "wb");
        fwrite($ifp, base64_decode($base64_string));
        fclose($ifp);
        return($output_file);
    }

    public function getExtensionFromType($type, $default = ''){
        static $extensions = array(
          'image/bmp' => 'bmp',
          'image/cewavelet' => 'wif',
          'image/cis-cod' => 'cod',
          'image/fif' => 'fif',
          'image/gif' => 'gif',
          'image/ief' => 'ief',
          'image/jp2' => 'jp2',
          'image/jpeg' => 'jpg',
          'image/jpm' => 'jpm',
          'image/jpx' => 'jpf',
          'image/pict' => 'pic',
          'image/pjpeg' => 'jpg',
          'image/png' => 'png',
          'image/targa' => 'tga',
          'image/tiff' => 'tif',
          'image/vn-svf' => 'svf',
          'image/vnd.dgn' => 'dgn',
          'image/vnd.djvu' => 'djvu',
          'image/vnd.dwg' => 'dwg',
          'image/vnd.glocalgraphics.pgb' => 'pgb',
          'image/vnd.microsoft.icon' => 'ico',
          'image/vnd.ms-modi' => 'mdi',
          'image/vnd.sealed.png' => 'spng',
          'image/vnd.sealedmedia.softseal.gif' => 'sgif',
          'image/vnd.sealedmedia.softseal.jpg' => 'sjpg',
          'image/vnd.wap.wbmp' => 'wbmp',
          'image/x-bmp' => 'bmp',
          'image/x-cmu-raster' => 'ras',
          'image/x-freehand' => 'fh4',
          'image/x-ms-bmp' => 'bmp',
          'image/x-png' => 'png',
          'image/x-portable-anymap' => 'pnm',
          'image/x-portable-bitmap' => 'pbm',
          'image/x-portable-graymap' => 'pgm',
          'image/x-portable-pixmap' => 'ppm',
          'image/x-rgb' => 'rgb',
          'image/x-xbitmap' => 'xbm',
          'image/x-xpixmap' => 'xpm',
          'image/x-xwindowdump' => 'xwd'
        );
    
        return !$type ? $default : (isset($extensions[$type]) ? '.'.$extensions[$type] : $default);
    }

    public function getTransferredParams(){
        return $this->transferredParams;
    }

    /**
     * Валидация входных параметров
     *
     * $params	входные параметры экшена
     */
    public function validate($params) 
	{
        $values = array();

        $this->transferredParams = $params;

        foreach ($this->parameters as $name => $conf) {
            try {
                $values[$name] = $conf['validator']->clean(isset($params[$name]) ? $params[$name] : null);
            } catch (agInvalidValueException $ex) {
                // если связанный код ошибки не установлен - выбрасываем дефолтовое исключение
                if ($conf['errorcode'] && isset($this->exceptions[$conf['errorcode']])) {
                    $this->throwActionException($conf['errorcode']);
                } else {
                    throw new agActionException(str_replace('Invalid value', 'некорректное значение', sprintf('Ошибка в поле "%s": %s', $conf['description'], $ex->getMessage())), Errors::BAD_PARAMETER, null, $name);
                }
            }
        }
        $this->values = $values;
    }
}
