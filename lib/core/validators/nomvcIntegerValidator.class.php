<?php

/**
 * Валидатор целых чисел
 */
class nomvcIntegerValidator extends nomvcBaseValidator {

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
		
		if (!preg_match('/^\-?\d+$/', $value)) {
			throw new nomvcInvalidValueException($value);
		}
		
		$value = (int) $value;
		
		$min = $this->getOption('min');
		if ($min && $value < $min) {
			throw new nomvcInvalidValueException($value, 'min');
		}
		$max = $this->getOption('max');
		if ($max && $value > $max) {
			throw new nomvcInvalidValueException($value, 'max');
		}
		
		return $value;
	}

}
