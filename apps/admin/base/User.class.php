<?php

class User extends nomvcDatabaseUser {

	protected $dbTable = 'T_MEMBER';		// таблица, в которой живут пользователи
	protected $dbLogin = 'login';		// поле с логином полдьзователя
	protected $dbPassword = 'passwd';	// поле с паролем пользователя

}
