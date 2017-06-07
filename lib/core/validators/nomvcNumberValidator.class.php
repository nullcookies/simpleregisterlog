<?php

/**
 * Валидатор чисел
 */
class nomvcNumberValidator extends nomvcBaseValidator {

	protected function init() {
		parent::init();
		$this->addOption('decimal_in', false, ',.');
		$this->addOption('decimal_out', false, '.');
		$this->addOption('min', false, false);
		$this->addOption('max', false, false);
	}

	public function clean($value) {
		$value = parent::clean($value);

		if ($this->getOption('required') == false && $value == null) {
			return null;
		}
		$value = str_replace(' ', '', $value);

		$regexp_check = sprintf('/^(\-?\d+)?([%s]\d+)?$/', $this->getOption('decimal_in'));
		$regexp_clean = sprintf('/[%s]/', $this->getOption('decimal_in'));
		if (!preg_match($regexp_check, $value)) {
			throw new nomvcInvalidValueException($value);
		}

		if(!empty($value)){
			//проверка на минимальное значение
			if(!empty($this->getOption("min")) && $value < $this->getOption("min")){
				throw new nomvcInvalidValueException("Введённое значение меньше разрешенного ". $this->getOption("min"));
			}

			//проверка на максимальное значение
			if(!empty($this->getOption("max")) && $value > $this->getOption("max")){
				throw new nomvcInvalidValueException("Введённое значение больше разрешенного ". $this->getOption("max"));
			}
		}
		
		return (float) preg_replace($regexp_clean, $this->getOption('decimal_out'), $value);
	}

}
