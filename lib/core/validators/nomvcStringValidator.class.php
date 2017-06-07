<?php

/**
 * Валидатор строковых значений
 */
class nomvcStringValidator extends nomvcBaseValidator {

	protected function init() {
		parent::init();
		$this->addOption('min', false, false);
		$this->addOption('max', false, false);
	}

	public function clean($value) {
		$value = parent::clean($value);
		
		if ($this->addOption('required') == false && $value == null) {
			return null;
		}

		$min = $this->getOption('min');
		if ($min && mb_strlen($value, 'UTF-8') < $min) {
			throw new nomvcInvalidValueException($value, 'min');
		}
		$max = $this->getOption('max');
		if ($max && mb_strlen($value, 'UTF-8') > $max) {
			throw new nomvcInvalidValueException($value, 'max');
		};
		return (string) $value;
	}

}
