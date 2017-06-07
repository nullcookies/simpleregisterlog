<?php

/**
 * Класс - описатель контекста выполнения приложения, предназначен для соединения отдельных
 * компонентов в одно целое.
 */
abstract class nomvcContext {

	// константы режима работы среды выполнения
	const CONTEXT_WEB = 'web';
	const CONTEXT_TASK = 'task';
	
	const ENV_PROD	= 'prod';
	const ENV_DEBUG	= 'debug';
		
	// список директорий
	protected $directories = array();
	
	// конфиг среды окружения
	protected $config;
	
	// список модулей
	protected $modules = array();
	
	protected static $instance;
	
	public static function initContext($contextType, $contextEnv) {
		$instance = null;
		switch($contextType) {
		case self::CONTEXT_WEB:  $contextClass = 'WebContext';  break;
		case self::CONTEXT_TASK: $contextClass = 'TaskContext'; break;
		}
		if ($contextClass) {
			self::$instance = new $contextClass($contextEnv);
			return true;
		} else {
			return false;
		}
	}
		
	public static function getInstance() {
		return self::$instance;
	}

	/**
	 * Создание контекста 
	 *
	 * $env - среда окружения выбранная при создании
	 */
	public function __construct($env = self::ENV_PROD) {
		$this->configureDirs();
		$this->configureContext($env);
	}
	
	/** конфигурация директорий **/
	protected function configureDirs() {
		$this->setDir('base', dirname(dirname(dirname(dirname(__FILE__)))));
		$this->setDir('config', $this->getDir('base').'/config');
		$this->setDir('templates', $this->getDir('base').'/templates');
	}
	
	/** конфигурация среды окружения */
	protected function configureContext($env) {
		$config = sfYaml::load($this->getDir('config').'/context.yml');		
		$config = array_merge($config[$env], $config['all']);
		foreach ($config['ini_set'] as $key => $val) {
			ini_set($key, $val);
		}
		$this->config = $config;
		return $config;
	}	
	
	/**
	 * Добавляет запись в список директорий
	 * 
	 * $name	название директории
	 * $val		абсолютный путь
	 */
	protected function setDir($name, $val) {
		$this->directories[$name] = $val;
	}
	
	/**
	 * возвращает путь запрошенной директории или же значение по умолчанию
	 * если указанная директория не найдена
	 *
	 * $name	код директории
	 * $default	значение по умолчанию
	 */
	public function getDir($name, $default = null) {
		if (isset($this->directories[$name])) {
			return $this->directories[$name];
		} else {
			return $default;
		}
	}
	
	/** возвращает параметр из конфиг контекста */
	public function getConfigVal($name, $default = null) {
		return isset($this->config[$name]) && $this->config[$name] ? $this->config[$name] : $default;
	}
	
	public function addModule($module) {
		if (!in_array($module, $this->modules)) $this->modules[] = $module;
	}
	
	public function getModules() {
		return $this->modules;
	}	

}
