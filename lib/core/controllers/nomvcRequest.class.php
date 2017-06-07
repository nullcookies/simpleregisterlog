<?php

/**
 * Абстрактный запрос в ваккууме
 */
abstract class nomvcRequest {

	// ссылка на контекст
	protected $context;

	/** Конструктор */
	public function __construct($context) {
		$this->context = $context;
		$this->init();
	}
	
	/**
	 * Инициализация
	 */
	abstract public function init();

	/**
	 * Возвращает параметр запроса
	 */
	abstract public function getParameter($parameter, $default = null);

	/**
	 * Возвращает pfghjityysq URI
	 */
	public function getUri() {
		return isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
	}
	
	/**
	 * Возвращает ip адрес клиента
	 */
	public function getRemoteIp() {
		return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
	}

}
