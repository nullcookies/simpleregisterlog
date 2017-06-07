<?php

/**
 * Абстрактный пользователь, данные которого живут в сессиии
 */
abstract class nomvcSessionUser extends nomvcAbstractUser {

	public function init() { session_start(); }
	public function signout() { session_destroy(); }

	public function getAttribute($name, $default = null) {
		return isset($_SESSION['session_user_data/'.$name]) ? $_SESSION['session_user_data/'.$name] : $default;
	}
	
	public function setAttribute($name, $value) {
		$_SESSION['session_user_data/'.$name] = $value;
	}

}
