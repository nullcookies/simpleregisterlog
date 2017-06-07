<?php

class LoginForm extends nomvcAbstractForm {

	// инициализация формы
	public function init() {
		parent::init();
		// настраиваем виджеты
		$this->addWidget(new nomvcInputMsisdnWidget('Логин', 'login', array('label-width' => 4)));
		$this->addWidget(new nomvcInputPasswordWidget('Пароль', 'password', array('label-width' => 4)));
		$this->addWidget(new nomvcButtonWidget('Войти', 'log_in', array('type' => 'submit')));
		// настраиваем валидаторы
		$this->addValidator('login', new nomvcStringValidator(array('required' => true)));
		$this->addValidator('password', new nomvcStringValidator(array('required' => true)));
	}

}
