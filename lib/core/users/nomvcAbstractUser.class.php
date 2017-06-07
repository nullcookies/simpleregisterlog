<?php

/**
 * Абстрактный юзверь, здесь описано всё то, что он должен уметь
 */
abstract class nomvcAbstractUser {

	// ссылка на контекст
	protected $context;

	/** Конструктор */
	public function __construct($context) {
		$this->context = $context;
		$this->init();
	}

	/**
	 * Выполняет проверку на авторизованность пользователя
	 */
	public function hasAuth() {
		return $this->getAttribute('has_auth', false);
	}

	/**
	 * Инициализация пользователя
	 */
	public abstract function init();

	/**
	 * авторизует пользователя
	 */
	public abstract function signin($login, $password);

	/**
	 * выполняет разлогинивание пользователя
	 */
	public abstract function signout();

	/**
	 * Возвращает атрибут пользователя
	 */
	public abstract function getAttribute($name, $default = null);

	/**
	 * Выполняет установку атрибута пользователя
	 */
	public abstract function setAttribute($name, $value);

}
