<?php

class nomvcInputPasswordWidget extends nomvcInputWidget {

	protected function init() {
		parent::init();
		$this->setAttribute('type', 'password');
	}
	
	public function renderControl($value, $attributes = array()) {
		return parent::renderControl(null, $attributes);
	}

}
