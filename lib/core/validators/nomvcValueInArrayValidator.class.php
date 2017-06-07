<?php

/**
 * Валидатор строковых значений
 */
class nomvcValueInArrayValidator extends nomvcBaseValidator {

	protected function init() {
		parent::init();
		$this->addOption('options', true, false);
	}

	public function clean($value) {
		
		$value = parent::clean($value);
		
		if ($this->addOption('required') == false && $value == null) {
			return null;
		}
		
		$arr = array_flip($this->getOption('options'));
		if (!isset($arr[$value])) {
			throw new nomvcInvalidValueException($value, 'invalid');
		}
		
		return $value;
	}
	
}
