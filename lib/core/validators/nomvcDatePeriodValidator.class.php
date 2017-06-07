<?php

/**
 * Валидатор периода даты
 */
class nomvcDatePeriodValidator extends nomvcAbstractPeriodValidator {

	protected function init() {
		parent::init();
		$this->addOption('format', false, DateHelper::HTMLD_FORMAT);
	}
	
	public function getDateFromString($value) {
		$regexp = DateHelper::getRegexpFromFormat($this->getOption('format'));
		$dbRegexp = DateHelper::getRegexpFromFormat(DateHelper::DBD_FORMAT);
		if (preg_match($regexp, $value, $match)) {
			return DateHelper::dateConvert($this->getOption('format', DateHelper::HTMLD_FORMAT), DateHelper::DBD_FORMAT, $value);
		} elseif (preg_match($dbRegexp, $value, $match)) {
			return $value;
		}
		throw new nomvcInvalidValueException($value);
		
	}
	
}
