<?php

/**
 * Базовый контроллер
 */
abstract class nomvcBaseController {

	/** @var context ссылка на контекст */
	protected $context;
	/** @var controller ссылка на контроллер, из которого этот компонент был вызван */
	protected $parentController;
	/** @var string текущий путь к контроллеру */
	protected $uri;
	/** @var User Текущий пользователь */
	protected $user;

	/**
	 * Конструктор
	 * @param context $context		Контекст выполнения
	 * @param controller $parentController	Контроллер, из которого этот компонент был вызван
	 */
	public function __construct($context = null, $parentController = null) {
		if ($context) {
			$this->context = $context;
		} else {
			$this->context = Context::getInstance();
		}

		$this->user = $this->context->getUser();

		$this->init();
		$this->parentController = $parentController;
		if ($this->parentController == null) {
			$this->uri = $this->context->getRequest()->getUri();
		} else {
			$this->uri = $this->parentController->getNextUri();
		}
		preg_match('|^/([^/]+)(/(.*))?$|imu', $this->uri, $match);
		$this->currUriPart = isset($match[1]) ? $match[1] : null;
		$this->nextUri = isset($match[2]) ? $match[2] : null;
	}

	/** возвращает путь текущего контроллера */
	public function getCurrentUriPart() {
		return $this->currUriPart;
	}

	/** возвращает путь к следующему контроллеру */
	public function getNextUri() {
		return $this->nextUri;
	}

	/** преобразование регистра */
	public function camelize($scored) {
		return lcfirst(implode('',array_map('ucfirst',array_map('strtolower',explode('_', $scored)))));
	}

	function underscore($cameled) {
		return implode('_', array_map('strtolower',
			preg_split('/([A-Z]{1}[^A-Z]*)/', $cameled, -1, PREG_SPLIT_DELIM_CAPTURE |PREG_SPLIT_NO_EMPTY)));
	}

	public function redirect($url = null) {
		if (is_null($url)) {
			header('Location: '.$_SERVER['HTTP_REFERER']);
		} else {
			header('Location: http://'.$_SERVER['HTTP_HOST'].$url);
		}
		exit();
	}

	/** Первичная инициализация контроллера */
	abstract protected function init();

	/** Запуск контроллера */
	abstract public function run();

	/** Возвращает путь текущего контроллера */
	protected function makeUrl() {
		return '';
	}
}
