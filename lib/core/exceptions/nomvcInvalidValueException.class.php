<?php

/**
 * Эксепшен, выбрасываемый валидатором при некорректном значении
 */
class nomvcInvalidValueException extends nomvcBaseException {

	protected $value;
	protected $reason;

	public function __construct($value, $reason = 'invalid') {
		if (is_array($value)){
			$value = implode(", ", $value);
		}
		parent::__construct(sprintf('Invalid value "%s"', $value));
		$this->value = $value;
		$this->reason = $reason;
	}
	
	public function getValue() {
		return $this->value;
	}
	
	public function getReason() {
		return $this->reason;
	}
}
