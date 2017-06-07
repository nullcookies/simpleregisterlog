<?php

abstract class nomvcBaseTask extends nomvcBaseController {

	protected $validators = array();	// валидаторы формы
	protected $valueErrors = array();	// ошибки в полях формы

	public function run() {}
	
	public function exec($params) {
		if (!$this->validate($params)) {
			throw new Exception("BAD PARAMS");
		}
	}
	
	/**
	 * Запускает процесс проверки таска
	 */
	public function validate($data) {
		$values = array();
		foreach ($this->validators as $name => $validator) {
			try {
				if (!isset($data[$name])) $data[$name] = null;
				$values[$name] = $validator->clean(isset($data[$name]) ? $data[$name] : null);
			} catch (nomvcInvalidValueException $ex) {
				$this->errorValues[$name] = $data[$name];
				$this->valueErrors[$name] = $ex->getReason();
			}
		}
		$this->values = $values;
		return count($this->valueErrors) == 0;
	}
	
	/**
	 * Метод для добавления валидатора
	 */
	public function addParameter($name, $validator) {
		$this->validators[$name] = $validator;
	}
	
	/**
	 * Возвращает проверенное значение формы
	 */
	public function getValue($name, $default = null) {
		return isset($this->values[$name]) ? $this->values[$name] : $default;
	}
	
	public function makeUrl() {
		return '';
	}

}
