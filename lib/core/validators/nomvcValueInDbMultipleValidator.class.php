<?php

/**
 * Валидатор строковых значений
 */
class nomvcValueInDbMultipleValidator extends nomvcValueInDbValidator {

	public function clean($values) {
		if ($values == null) {
			$values = array();
		}
		foreach ($values as $key => $value) {
			if ($key == 'set_all' && $value == 'on') {
				unset($values[$key]);
			} else {
				$values[$key] = parent::clean($value);
			}
		}
		if (count($values) == 0 && $this->getOption('required')) {
			throw new nomvcInvalidValueException(null, 'required');
		}
		
		return $values;
	}
	
}
