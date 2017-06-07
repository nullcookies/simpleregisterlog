<?php

class nomvcColorValidator extends nomvcBaseValidator {

	protected function init() {
		parent::init();
	}

	public function clean($value) {
		$value = parent::clean($value);
		
		if ($this->addOption('required') == false && $value == null) {
			return null;
		}
		
		if (!preg_match('/^#([0-9a-f]{6})$/i', $value, $matches)) {
			throw new nomvcInvalidValueException($value);
		}
				
		$value = hexdec($matches[1]);
		return $value;
	}

}
