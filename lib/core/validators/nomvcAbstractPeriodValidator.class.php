<?php

/**
 * Абстрактный валидатор периода времени
 */
abstract class nomvcAbstractPeriodValidator extends nomvcBaseValidator {

	public function clean($value) {
		if (!$value) {
			return null;
		}
		
		$dt = array();

		if (isset($value['from']) && $value['from'] > '') {
			$dt['from'] = $this->getDateFromString($value['from']);
		}
		if (isset($value['to']) && $value['to'] > '') {
			$dt['to'] = $this->getDateFromString($value['to']);
		}
		return $dt;
	}
}
