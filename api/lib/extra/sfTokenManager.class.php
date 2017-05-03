<?php

class sfTokenManager extends agAbstractComponent
{
	private $action_context;
	private $uuid;
	private $msisdn;
	private $token;
	private $code;

	protected function init(){
		$this->salt = $this->context->getConfigVal('salt');

		$this->token_life_time = $this->context->getConfigVal('token_life_time'); //minute

		$this->enable_check_exist_token = $this->context->getConfigVal('enable_check_exist_token');

		$this->enable_limit_get_token = $this->context->getConfigVal('enable_limit_get_token');
		$this->time_limit_get_token = $this->context->getConfigVal('time_limit_get_token'); //minute
		$this->limit_get_token_per_time = $this->context->getConfigVal('limit_get_token_per_time');

		$this->enable_limit_error_check_token = $this->context->getConfigVal('enable_limit_error_check_token');
		$this->time_limit_error_check_token = $this->context->getConfigVal('time_limit_error_check_token'); //minute
		$this->limit_error_check_token_per_time = $this->context->getConfigVal('limit_error_check_token_per_time');
	}

	public function __construct($action_context){
		$this->action_context = $action_context;
		parent::__construct($action_context->context);
	}
 
	//уже существует токен для ip & uuid & msisdn не экспайред
	private function check1(){
		if ((isset($this->uuid) && !empty($this->uuid)) && (isset($this->msisdn) && !empty($this->msisdn))){
			$stmt = $this->context->getDb()->prepare('
			select count(*) as cnt
			from T_SECURITY_TOKEN_LOG
			where NET = :NET
			and UUID = :UUID
			and MSISDN = :MSISDN
			and dt > dateadd(minute, -'.$this->token_life_time.', getdate())
			and has_verify = 1
			');

			$stmt->bindValue('NET', $this->getIp());
			$stmt->bindValue('UUID', $this->uuid);
			$stmt->bindValue('MSISDN', $this->msisdn);
			$stmt->execute();

			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$cnt = $row['cnt'];
			$stmt->closeCursor();

			//var_dump($cnt); exit;
			if ($cnt > 0){
				throw new agGlobalException('exist confirm and don\'t expired token for user', agAbstractApiController::EXIST_TOKEN_FOR_USER);
			}
		}
	}

	//лимит на кол-во попыток генерации токенов в ед.времени для ip & uuid & msisdn
	private function check2(){
		if ((isset($this->uuid) && !empty($this->uuid)) && (isset($this->msisdn) && !empty($this->msisdn))){
			$stmt = $this->context->getDb()->prepare('
			select count(*) as cnt
			from T_SECURITY_TOKEN_LOG
			where NET = :NET
			and UUID = :UUID
			and MSISDN = :MSISDN
			and dt > dateadd(minute, -'.$this->time_limit_get_token.', getdate())
			');

			$stmt->bindValue('NET', $this->getIp());
			$stmt->bindValue('UUID', $this->uuid);
			$stmt->bindValue('MSISDN', $this->msisdn);
			$stmt->execute();

			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$cnt = $row['cnt'];
			$stmt->closeCursor();

			if ($cnt >= $this->limit_get_token_per_time){
				throw new agGlobalException('limit get token per time for user', agAbstractApiController::LIMIT_GET_TOKEN_PER_TIME);
			}
		}
	}

	//лимит на кол-во не успешных попыток прдтверждения токена в ед. времени
	private function check3(){
		if ((isset($this->uuid) && !empty($this->uuid)) && (isset($this->msisdn) && !empty($this->msisdn)) && (isset($this->token) && !empty($this->token))){
			$stmt = $this->context->getDb()->prepare('
			select count(*) as cnt
			from T_LOG
			where NET = :NET
			and UUID = :UUID
			and MSISDN = :MSISDN
			and ID_API_ACTION = 6
			and RESPONSE_CODE = 101
			and dt > dateadd(minute, -'.$this->time_limit_error_check_token.', getdate())
			');

			$stmt->bindValue('NET', $this->getIp());
			$stmt->bindValue('UUID', $this->uuid);
			$stmt->bindValue('MSISDN', $this->msisdn);
			$stmt->execute();

			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$cnt = $row['cnt'];
			$stmt->closeCursor();

			//var_dump($cnt); exit;

			if ($cnt >= $this->limit_error_check_token_per_time){
				$this->blockToken();
				throw new agGlobalException('limit error check token per time for user', agAbstractApiController::LIMIT_ERROR_CHECK_TOKEN_PER_TIME);
			}
		}
	}

	private function blockToken(){
		$stmt = $this->context->getDb()->prepare('
		update T_SECURITY_TOKEN_LOG 
		set IS_BLOCK = 1, DT_BLOCK = getdate() 
		where NET = :NET
		and UUID = :UUID
		and MSISDN = :MSISDN
		and TOKEN = :TOKEN
		and IS_BLOCK = 0
		');

		$stmt->bindValue('NET', $this->getIp());
		$stmt->bindValue('UUID', $this->uuid);
		$stmt->bindValue('MSISDN', $this->msisdn);
		$stmt->bindValue('TOKEN', $this->token);
		$res = $stmt->execute();

		return $stmt->rowCount() > 0?true:false;
	}

	private function checkVerify(){
		if ((isset($this->uuid) && !empty($this->uuid)) && (isset($this->msisdn) && !empty($this->msisdn)) && (isset($this->token) && !empty($this->token))){
			$stmt = $this->context->getDb()->prepare('
			select count(*) as cnt
			from T_SECURITY_TOKEN_LOG
			where NET = :NET
			and UUID = :UUID
			and MSISDN = :MSISDN
			and TOKEN = :TOKEN
			and HAS_VERIFY = 1
			');

			$stmt->bindValue('NET', $this->getIp());
			$stmt->bindValue('UUID', $this->uuid);
			$stmt->bindValue('MSISDN', $this->msisdn);
			$stmt->bindValue('TOKEN', $this->token);
			$stmt->execute();

			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$cnt = $row['cnt'];
			$stmt->closeCursor();

			//var_dump($cnt); exit;
			if ($cnt > 0){
				throw new agGlobalException('token already verify', agAbstractApiController::TOKEN_ALREADY_VERIFY);
			}
		}
	}


	private function setVerify(){
		$this->checkVerify();
	
		if ($this->enable_limit_error_check_token)
			$this->check3();
			
		$stmt = $this->context->getDb()->prepare('
		update T_SECURITY_TOKEN_LOG 
		set HAS_VERIFY = 1, DT_VERIFY = getdate() 
		where NET = :NET
		and UUID = :UUID
		and MSISDN = :MSISDN
		and TOKEN = :TOKEN
		and CODE = :CODE
		and has_verify = 0
		and is_block != 1');

		$stmt->bindValue('NET', $this->getIp());
		$stmt->bindValue('UUID', $this->uuid);
		$stmt->bindValue('MSISDN', $this->msisdn);
		$stmt->bindValue('TOKEN', $this->token);
		$stmt->bindValue('CODE', $this->code);
		$res = $stmt->execute();

		return $stmt->rowCount() > 0?true:false;
	}

	private function saveToken($is_auth){
		//инсертим в отдельный лог
		//try {
			$stmt = $this->context->getDb()->prepare('insert into T_SECURITY_TOKEN_LOG(NET, UUID, MSISDN, TOKEN, CODE, IS_AUTH, IS_BLOCK) VALUES(:NET, :UUID, :MSISDN, :TOKEN, :CODE, :IS_AUTH, 0)');
			$stmt->bindValue('NET', $this->getIp());
			$stmt->bindValue('UUID', $this->uuid);
			$stmt->bindValue('MSISDN', $this->msisdn);
			$stmt->bindValue('TOKEN', $this->token);
			$stmt->bindValue('CODE', $this->code);
			$stmt->bindValue('IS_AUTH', $is_auth?1:0);
			$stmt->execute();
			$stmt->closeCursor();
		//}
		//catch(exception $e){ var_dump($e->getMessage()); exit;}
	}

	private function generate($salt = false){    
	    $this->salt = $salt?$salt:$this->salt;
	   
		return sha3($this->uuid.$this->msisdn.time().$this->salt);
	}
	
	public function getProlongation($uuid, $msisdn, $token){
        $stmt = $this->context->getDb()->prepare('
            select top 1 token, has_verify
            from T_SECURITY_TOKEN_LOG
            where MSISDN = :MSISDN
            and UUID = :UUID
            --and NET = :NET
            order by DT DESC
        ');

        $stmt->bindValue('MSISDN', $msisdn);
        $stmt->bindValue('UUID', $uuid);
        $stmt->bindValue('NET', $this->getIp());            
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $last_token = isset($row['token'])?$row['token']:null;
        $has_verify = isset($row['has_verify'])?$row['has_verify']:0;
        $stmt->closeCursor();
	
	    if ($token == $last_token && $has_verify == 1){
	        $new_token = $this->generate($token);
	        
	        $stmt = $this->context->getDb()->prepare('insert into T_SECURITY_TOKEN_LOG(NET, UUID, MSISDN, TOKEN, CODE, IS_AUTH, IS_BLOCK, HAS_VERIFY, DT_VERIFY) VALUES(:NET, :UUID, :MSISDN, :TOKEN, :CODE, :IS_AUTH, 0, 1, GETDATE())');
		    $stmt->bindValue('NET', $this->getIp());
		    $stmt->bindValue('UUID', $uuid);
			$stmt->bindValue('MSISDN', $msisdn);
			$stmt->bindValue('TOKEN', $new_token);
			$stmt->bindValue('CODE', null);
			$stmt->bindValue('IS_AUTH', 0);
			$stmt->execute();
			$stmt->closeCursor();

	        //var_dump($last_token, $has_verify, $new_token); exit;
			return array('token' => $new_token, 'token_life_time' => $this->token_life_time);
	    }
	        
	    return false;
	}

	public function get($uuid, $msisdn, $code){
		$is_auth = true;
		$this->uuid = $uuid;
		$this->msisdn = $msisdn;
		$this->code = $code;

		if ($this->enable_check_exist_token)
			$this->check1();

		if ($this->enable_limit_get_token)
			$this->check2();


		//var_dump('yes'); exit;
		//если это не авторизация, то отправляем дополнительное смс
		if(!$this->code){
			$is_auth = false;
			$r = $this->action_context->filterResponse($this->action_context->getP2PClient()->check_client(substr($msisdn,1)));
			
			//сохранение
			if (isset($r['status']) && $r['status'] == 0){
				if (isset($r['code'])){
					$this->code = $r['code'];
				}
			}
		}

		$this->token = $this->generate($this->code);
		$this->saveToken($is_auth);

		return array('is_auth' => $is_auth, 'token' => $this->token, 'token_life_time' => $this->token_life_time);
	}

	public function verify($uuid, $msisdn, $token, $code){
		$this->uuid = $uuid;
		$this->msisdn = $msisdn;
		$this->code = $code;
		$this->token = $token;
		
		return $this->setVerify();
	}
}
