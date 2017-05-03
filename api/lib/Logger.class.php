<?php

class Logger extends agLogger {	
	private $module = null;
	private $action = null;
	private $input = null;
	private $output = null;

	protected function init() {
		$this->enable_headers_request_in_file_log = $this->context->getConfigVal('enable_headers_request_in_file_log',false);

		$this->log_dir = $this->context->getConfigVal('log_dir');
		
		$this->enable_api_db_log = $this->context->getConfigVal('enable_api_db_log',false);			
	}

	protected function getToken(){
		$data = @json_decode($this->input);
		return @$data->request->params->token;
	}
	
	protected function getMsisdn(){
		$data = @json_decode($this->input);
		return @$data->request->params->msisdn;
	}

	protected function getUUID(){
		$data = @json_decode($this->input);
		return @$data->request->params->uuid;
	}

	protected function getHeaders(){
		return array('headers' => getallheaders());
	}

	protected function getIdApiAction(){
		$conn = $this->context->getDb();
		$id_api_action = null;

		try {
			$stmt = $conn->prepare('select top 1 id_api_action from T_API_ACTION where name = :action');
			$stmt->bindValue('action', $this->action);
			$stmt->execute();
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$stmt->closeCursor();

			if (isset($row['id_api_action'])){
				$id_api_action = $row['id_api_action'];
			}
			else{
				try {
					$stmt = $conn->prepare('insert into T_API_ACTION (name) values (:action)');
					$stmt->bindValue('action', $this->action);
					$stmt->execute();
					$id_api_action = $conn->lastInsertId();
				}
				catch(exception $e){}
			}
		}
		catch(exception $e){}
	
		//var_dump($id_api_action); exit;
		return $id_api_action;
	}


	protected function getIdAppVersion(){
		$conn = $this->context->getDb();
		$headers = $this->getHeaders();

		preg_match('/^PSBank\/([\d]\.[\d]+)/iu', @$headers['headers']['User-Agent'], $matches);
		$version = @$matches[1];
		$id_app_version = null;

		try {		
			$stmt = $conn->prepare('select top 1 id_app_version from T_APP_VERSION where name = :version');
			$stmt->bindValue('version', $version);
			$stmt->execute();
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$stmt->closeCursor();

			if (isset($row['id_app_version'])){
				$id_app_version = $row['id_app_version'];
			}
			elseif ($version){
				try {
					$stmt = $conn->prepare('insert into T_APP_VERSION (name) values (:version)');
					$stmt->bindValue('version', $version);
					$stmt->execute();
					$id_app_version = $conn->lastInsertId();
				}
				catch(exception $e){}
			}
		}
		catch(exception $e){}

		//var_dump($id_app_version); exit;
		return $id_app_version;
	}
		
	protected function getIdModel(){
		$conn = $this->context->getDb();
		$headers = $this->getHeaders();

		//Android
		//preg_match('/(Android .*)\;/iu', @$headers['headers']['User-Agent'], $s);
		$s = $this->context->getUser()->getAttribute('os');
		$id_model = null;
		if ($s == 2) {
			preg_match('/Device\:(.*)/iu', @$headers['headers']['User-Agent'], $matches);
			$model = @$matches[1];			
		}
		//IOS
		else {
			preg_match('/\((.*)\;/iUu', @$headers['headers']['User-Agent'], $matches);
			$model = @$matches[1];
		}

		try {		
			$stmt = $conn->prepare('select top 1 id_model from T_MODEL_PHONE where name = :model');
			$stmt->bindValue('model', $model);
			$stmt->execute();
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$stmt->closeCursor();

			if (isset($row['id_model'])){
				$id_model = $row['id_model'];
			}
			elseif ($model){
				try {
					$stmt = $conn->prepare('insert into T_MODEL_PHONE (name) values (:model)');
					$stmt->bindValue('model', $model);
					$stmt->execute();
					$id_model = $conn->lastInsertId();
				}
				catch(exception $e){}
			}
		}
		catch(exception $e){}

		//var_dump($id_model); exit;
		return $id_model;
	}

	protected function getIdOS(){
		$conn = $this->context->getDb();
		$headers = $this->getHeaders();
		
		//$this->context->getUser()->getAttribute('os')

		//Android
		//preg_match('/(Android .*)\;/iu', @$headers['headers']['User-Agent'], $s);
		$s = $this->context->getUser()->getAttribute('os');
		$id_os = null;
		if ($s == 2) {
			preg_match('/(Android .*)\;/iu', @$headers['headers']['User-Agent'], $matches);
			$os = @$matches[1];
		}
		//IOS
		else {
			preg_match('/\;\s(.*)\;/iu', @$headers['headers']['User-Agent'], $matches);
			$os = @$matches[1];
		}
		
		try {		
			$stmt = $conn->prepare('select top 1 id_os from T_OS where name = :os');
			$stmt->bindValue('os', $os);
			$stmt->execute();
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$stmt->closeCursor();

			if (isset($row['id_os'])){
				$id_os = $row['id_os'];
			}
			elseif ($os){
				try {
					$stmt = $conn->prepare('insert into T_OS (name) values (:os)');
					$stmt->bindValue('os', $os);
					$stmt->execute();
					$id_os = $conn->lastInsertId();
				}
				catch(exception $e){}
			}
		}
		catch(exception $e){}

		//var_dump($id_os); exit;
		return $id_os;
	}

	protected function saveLogDb(){
		if (!is_array($this->output))
			$response = @json_decode($this->output,true);
		else
			$response = $this->output;

		if (@isset($response['result']))
			$response_code = $response['result'];
		elseif (@isset($response['response']['result']))
			$response_code = $response['response']['result'];
		else
			$response_code = 100;

		$msisdn = $this->getMsisdn();
		$uuid = $this->getUUID();

		//	var_dump($uuid); exit;
		//	var_dump($response_code); exit;
		if ($uuid){
			$conn = $this->context->getDb();
			$stmt = $conn->prepare('INSERT INTO T_LOG (MSISDN, UUID, ID_API_ACTION, ID_APP_VERSION, ID_MODEL, ID_OS, RESPONSE_CODE, NET, TOKEN) VALUES(:MSISDN, :UUID, :ID_API_ACTION, :ID_APP_VERSION, :ID_MODEL, :ID_OS, :RESPONSE_CODE, :NET, :TOKEN)');

			try {
				$stmt->bindValue('MSISDN', $msisdn);
				$stmt->bindValue('UUID', $uuid);
				$stmt->bindValue('ID_API_ACTION', $this->getIdApiAction());
				$stmt->bindValue('ID_APP_VERSION', $this->getIdAppVersion());
				$stmt->bindValue('ID_MODEL', $this->getIdModel());
				$stmt->bindValue('ID_OS', $this->getIdOS());
				$stmt->bindVAlue('RESPONSE_CODE', $response_code);
				$stmt->bindValue('NET', $this->getIp());
				$stmt->bindValue('TOKEN', $this->getToken());
				$stmt->execute();
				//var_dump('yes'); exit;
			}
			catch(exception $e){}
		}
	}

	protected function log() {
		if ($this->input && $this->output && $this->enable_api_db_log)
			$this->saveLogDb();

		$message = sprintf("%s\t%s\t%s/%s\t%s\t%s\n",
			date('Y-m-d H:i:s'),
			$this->stop_time - $this->start_time,
			$this->module,
			$this->action,
			str_replace(array("\r", "\n"), array('', ''), $this->input ? ($this->enable_headers_request_in_file_log?serialize($this->getHeaders()):'').serialize($this->input) : '-'),
			str_replace(array("\r", "\n"), array('', ''), $this->output ? serialize($this->output) : '-')
		);
		$filename = $this->log_dir.'/'.$this->context->getConfigVal('log_prefix','unprefix').'_main.log';
		file_put_contents($filename, $message, FILE_APPEND);
	}
	
	public function setController($module) {
		$this->module = $module;
	}
	
	public function setAction($action) {
		$this->action = $action;
	}
	
	public function setInput($input) {
		$this->input = $input;
	}
	
	public function setOutput($output) {
		$this->output = $output;
	}
		
}

?>
