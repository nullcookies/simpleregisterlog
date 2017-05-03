<?php


class agArrayValidator extends agBaseValidator {

	private $options = array();

	protected function init() {
		parent::init();
		$this->addOption('pre_trim',false, false);
	}
	
	public function clean($value) {
		$value = parent::clean($value);
		
		//проверка на то, что на входе массив
		if ($value){
//		var_dump(!($value instanceof stdClass)); exit;
			if (!is_array($value) && !($value instanceof stdClass))
				throw new agInvalidValueException('Is not array');
		}
		else
			$value = array();
	
		return $value;
	}
/*
	protected function checkOptions($options) {
		// проверяем, что нам не передали лишних опций
		foreach ($options as $option => $val) {
			if (!isset($this->options[$option])) {
				//throw new agAttributeException(sprintf('Incorrect option %s for validator %s', $option, get_class($this)));
			}
			$this->optionsVal[$option] = $val;
		}
		// проверяем, что все необходимые опции установлены
		foreach ($this->options as $option => $param) {
			if (!isset($this->optionsVal[$option])) {
				if ($param['required']) {
					throw new agAttributeException(sprintf('Option %s required for validator %s', $option, get_class($this)));
				}
				$this->optionsVal[$option] = $param['default'];
			}
		}
	}
*/
	public function __toString() {
		$params = array('JSON - массив значений');
		if ($this->getOption('required')) {
			$params[] = 'обязательный';
		} else {
			$params[] = 'не обязательный';
		}

		return implode(', ', $params);
	}
	
	public function getExample() {
		$min = 1;
		$max = 100;

		return json_encode(array(sfMoreSecure::crypto_rand_secure($min, $max),sfMoreSecure::crypto_rand_secure($min, $max),sfMoreSecure::crypto_rand_secure($min, $max)));
	}

}
