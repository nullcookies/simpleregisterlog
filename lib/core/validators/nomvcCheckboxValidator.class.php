<?php

/**
 * Валидатор чисел
 */
class nomvcCheckboxValidator extends nomvcBaseValidator {

	protected function init() {
		parent::init();
	}

	public function clean($value) {
		$value = parent::clean($value);
		if ($this->getOption('required') == false && $value == null) return 0;

		if(!empty($value)) return $value;

	}

}
