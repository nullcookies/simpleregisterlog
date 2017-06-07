<?php

/**
 * Абстрактный валидатор
 */
abstract class nomvcBaseValidator {

	// список опций
	private $options = array();
	// список значений опций
	protected $optionsVal = array();
	
	public final function __construct($options = array()) {
		$this->init();
		$this->checkOptions($options);
	}
	
	/** Инициализация валидатора */
	protected function init() {
		$this->addOption('required', false, false);
		$this->addOption('pre_trim', false, true);
		$this->addOption('example', false, null);
	}
	
	/**
	 * Проверка корректности настроек валидатора
	 */
	protected function checkOptions($options) {
		// проверяем, что нам не передали лишних опций
		foreach ($options as $option => $val) {
			if (!isset($this->options[$option])) {
				throw new nomvcAttributeException(sprintf('Incorrect option %s for validator %s', $option, get_class($this)));
			}
			$this->optionsVal[$option] = $val;
		}
		// проверяем, что все необходимые опции установлены
		foreach ($this->options as $option => $param) {
			if (!isset($this->optionsVal[$option])) {
				if ($param['required']) {
					throw new nomvcAttributeException(sprintf('Option %s required for validator %s', $option, get_class($this)));
				}
				$this->optionsVal[$option] = $param['default'];
			}
		}
	}
	
	/**
	 * Добавление опции
	 * 
	 * $option		название опции
	 * $required	обязательна ли опция?
	 * $default		значение по умолчанию
	 */
	protected function addOption($option, $required = false, $default = null) {
		$this->options[$option] = array(
			'default'	=> $default,
			'required'	=> $required
		);
	}
	
	/**
	 * Возвращает значение опции или значение по умолчанию
	 *
	 * $option	опция
	 * $default значение по умолчанию
	 */
	public function getOption($option, $default = null) {
		return $this->optionsVal[$option] ? $this->optionsVal[$option] : $default;
	}
	
	/**
	 * Возвращает значение опции или значение по умолчанию
	 *
	 * $option	опция
	 * $default значение по умолчанию
	 */
	public function setOption($option, $value) {
		if (!isset($this->options[$option])) {
			throw new nomvcAttributeException(sprintf('Incorrect option %s for validator %s', $option, get_class($this)));
		}
		$this->optionsVal[$option] = $value;
	}
	
	/** Вызов процесса валидации */
	public function clean($value) {	
		if ($this->getOption('pre_trim')) {
			$value = trim($value);
		}
		if ($value == null) {
			if ($this->getOption('required')) {
				throw new nomvcInvalidValueException($value, 'required');
			} else {
				return null;
			}
		}
		return $value;
	}

}
