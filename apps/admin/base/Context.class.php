<?php

class Context extends nomvcContext {

	protected $user = null;
	protected $request = null;
	protected $rootController = null;
	
	// текущее подключение к БД и хелпер
	private $dbconn = null;
	private $dbhelper = null;
	private $modelFactory = null;
	
	protected function configureDirs() {
		parent::configureDirs();
		$this->setDir('app_base', NOMVC_BASEDIR.'/apps/'.NOMVC_APPNAME);	
		$this->setDir('app_config', $this->getDir('app_base').'/config');	
		$this->setDir('app_templates', $this->getDir('app_base').'/templates');
		$this->setDir('app_reports', NOMVC_BASEDIR.'/web/reports');
	}
	
	protected function configureContext($env) {
		$rootConfig = parent::configureContext($env);
		$config = sfYaml::load($this->getDir('app_config').'/context.yml');
		$this->config = array_merge($rootConfig, $config['all'], $config[$env]);
		$this->user = new User($this);
		$this->request = new HttpRequest($this);
		$this->rootController = new RootController($this, null);
	}
	
	/** Получение коннекта к БД */
	public function getDb() {
		if (is_null($this->dbconn)) {
			$dbconf = $this->getConfigVal('db', null);
			$this->dbconn = new PDO($dbconf['dsn'], $dbconf['user'], $dbconf['passw']);			
            //$this->dbconn->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
            

			if (!$this->dbconn)
			    throw new agGlobalException('no connect to DB', agAbstractApiController::FATAL_ERROR);

            $this->dbconn->prepare('SET CHARSET \'utf8\'')->execute();
			$this->dbconn->prepare('ALTER SESSION SET NLS_DATE_FORMAT = "YYYY-MM-DD HH24:MI:SS"')->execute();
		}
		return $this->dbconn;
	}
	
	/** Получение инстанса хелпера */
	public function getDbHelper() {
		if (is_null($this->dbhelper)) {
			$this->dbhelper = new DbHelper($this);
		}
		return $this->dbhelper;
	}
	
	/** Получение инстанса хелпера */
	public function getModelFactory() {
		if (is_null($this->modelFactory)) {
			$this->modelFactory = new ModelFactory($this);
		}
		return $this->modelFactory;
	}
	
	public function getUser() {
		return $this->user;
	}
	
	public function getRequest() {
		return $this->request;
	}
	
	public function getRootController() {
		return $this->rootController;
	}
	
	public function getConfigVal($name, $default = null) {
		return isset($this->config[$name]) ? $this->config[$name] : $default;
	}
	
}
