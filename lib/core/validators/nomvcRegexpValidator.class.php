<?php

/**
 * Валидатор строковых значений
 */
class nomvcRegexpValidator extends nomvcStringValidator {

	protected function init() {
		parent::init();
		$this->addOption('regexp', true);
		$this->addOption('regexp_descr', false, 'регулярное выражение');
	}

	public function clean($value) {
		$value = parent::clean($value);
		
		if ($this->addOption('required') == false && $value == null) {
			return null;
		}
		
		if (!preg_match($this->getOption('regexp'), $value)) {
			throw new nomvcInvalidValueException($value);
		}
		
		return (string) $value;
	}
	
}
