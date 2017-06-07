<?php

/**
 * Валидатор периода даты/времени
 */
class nomvcDateTimePeriodValidator extends nomvcAbstractPeriodValidator {

	protected function init() {
		parent::init();
		$this->addOption('format', false, DateHelper::HTMLT_FORMAT);
	}
	
	public function getDateFromString($value) {
		$regexp = DateHelper::getRegexpFromFormat($this->getOption('format'));
		if (!preg_match($regexp, $value, $match)) {
			throw new nomvcInvalidValueException($value);
		}
		return DateHelper::dateConvert($this->getOption('format', DateHelper::HTMLT_FORMAT), DateHelper::DBT_FORMAT, $value);
	}

}
