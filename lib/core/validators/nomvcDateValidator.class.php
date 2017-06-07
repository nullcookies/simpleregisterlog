<?php

/**
 * Валидатор даты/времени
 */
class nomvcDateValidator extends nomvcBaseValidator {

	protected function init() {
		parent::init();
		$this->addOption('in_format', false, DateHelper::HTMLD_FORMAT);
		$this->addOption('out_format', false, DateHelper::DBD_FORMAT);
		$this->addOption('min', false, false);
		$this->addOption('max', false, false);
	}

	public function clean($value) {
		$value = parent::clean($value);
		
		if (!$value) {
			return (string) $value;
		}
        
		$timestamp = DateHelper::getDateTimeFromStr($this->getOption('in_format'), $value);
		if (!$timestamp) {
			throw new nomvcInvalidValueException($value);
		}

		$min = DateHelper::getDateTimeFromStr($this->getOption('in_format'), $this->getOption('min'));
		if ($min && ($timestamp < $min)) {
			throw new nomvcInvalidValueException($value, 'min');
		}
		$max = DateHelper::getDateTimeFromStr($this->getOption('in_format'), $this->getOption('max'));
		if ($max && ($timestamp > $max)) {
			throw new nomvcInvalidValueException($value, 'max');
		};
				
		return date($this->getOption('out_format'), $timestamp);
	}

}
