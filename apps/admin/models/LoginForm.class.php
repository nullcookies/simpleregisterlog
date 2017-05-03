<?php

class LoginForm extends nomvcAbstractForm {

	public function init() {
		parent::init();
		
		$this->addWidget(new nomvcInputTextWidget('Логин', 'login', array('label-width' => 4)));
		$this->addWidget(new nomvcInputPasswordWidget('Пароль', 'password', array('label-width' => 4)));
		$this->addWidget(new nomvcButtonWidget('Войти', 'log_in', array('type' => 'submit')));
		
		$this->addValidator('login', new nomvcStringValidator(array('required' => true)));
		$this->addValidator('password', new nomvcStringValidator(array('required' => true)));
	}

}
