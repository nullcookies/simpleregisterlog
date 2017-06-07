<?php

/**
 * Абстрактный пользователь, данные для авторизации которого можно найти в файле конфигурации
 */
abstract class nomvcConfigUser extends nomvcSessionUser {

	public function signin($login, $password) {
		$users = sfYaml::load($this->context->getDir('app_config').'/users.yml');
		if (isset($users[$login]) && $users[$login]['password'] == $password) {
			if (isset($users[$login]['attributes'])) {
				foreach($users[$login]['attributes'] as $attr => $val) {
					$this->setAttribute($attr, $val);
				}
			}
			$this->setAttribute('has_auth', true);
			return true;
		}
		return false;
	}

}
