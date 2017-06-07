<?php

/**
 * Инсключение некорректной авторизации
 * @param string $message Сообщение
 */
class nomvcNotAuthorizedException extends nomvcBaseException {
	public function __construct($message = NULL) {
		$message = empty($message) ? "Пользователь не авторизован" : $message;
		parent::__construct($message);
	}
}