<?php

abstract class nomvcBaseWidget {

	private $label;
	private $name;
	// список опций
	private $options = array();
	// список значений опций
	protected $optionsVal = array();
	private $attributes = array();

	public final function __construct($label, $name, $options = array(), $attributes = array()) {
		$this->label = $label;
		$this->name = $name;
		$this->init();
		$this->attributes = array_merge($this->attributes, $attributes);
		$this->checkOptions($options);
	}

	protected function init() {
		$this->addOption('size', false, 'sm');
		$this->addOption('label-width', false, 4);
		$this->addOption('has-error', false, false);
		$this->addOption('value', false, false);
		$this->addOption('helptext', false, false);
		$this->addOption('js_handler', false, false);
	}

	/**
	 * Проверка корректности настроек виджета
	 */
	protected function checkOptions($options) {
		// проверяем, что нам не передали лишних опций
		foreach ($options as $option => $val) {
			if (!isset($this->options[$option])) {
				throw new nomvcAttributeException(sprintf('Incorrect option "%s" for widget %s', $option, get_class($this)));
			}
			$this->optionsVal[$option] = $val;
		}
		// проверяем, что все необходимые опции установлены
		foreach ($this->options as $option => $param) {
			if (!isset($this->optionsVal[$option])) {
				if ($param['required']) {
					throw new nomvcAttributeException(sprintf('Option "%s" required for widget %s', $option, get_class($this)));
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
		return isset($this->optionsVal[$option]) && $this->optionsVal[$option] ? $this->optionsVal[$option] : $default;
	}


	/**
	 * Устанавливает значение опции
	 * @param string $name	Опция
	 * @param type $value	Значение
	 */
	public function setOption($option, $value) {
		if (!isset($this->options[$option])) {
			throw new nomvcAttributeException(sprintf('Incorrect option %s for validator %s', $option, get_class($this)));
		}
		$this->optionsVal[$option] = $value;
	}


	abstract public function renderForForm($formName);

	/** Возвращает имя контрола */
	public function getName()		{ return $this->name; }
	/** Возвращает лэйбл */
	public function getLabel()		{ return $this->label; }
	/** Возвращает опции */
	public function getOptions()		{ return $this->options; }
	/** Возвращает автрибуты */
	public function getAttributes()		{ return $this->attributes; }

	public function setAttribute($name, $value) {
		$this->attributes[$name] = $value;
	}

	public function getAttribute($name, $default = null) {
		return isset($this->attributes[$name]) ? $this->attributes[$name] : $default;
	}


	protected function compileAttribute($attributes) {
		$attributesCompiled = array();
		foreach ($attributes as $attr => $val) {
			if ($val === null) {
				$attributesCompiled[] = sprintf('%s', $attr);
			} elseif ($val !== false) {
				$attributesCompiled[] = sprintf('%s="%s"', $attr, $val);
			}
		}
		return $attributesCompiled;
	}


	public function getJSHandler($formName) {
		return $this->renderJSHandler($formName);

	}

	protected function renderJSHandler($formName) {
		if ($this->getOption('js_handler')) {
			$js_handler = $this->getOption('js_handler');
			if (!empty($js_handler['message'])){
				$badge = sprintf('<div id="handler_txt_%s" class="handler_txt">%s</div>',
					$this->getName(), $js_handler['message']);
			}
			else{
				$badge = "";
			}
			if (isset($js_handler['click'])) {
				$badge.= sprintf("<script> $('#%_%s').click(%s); </script>", $formName, $this->getName(), $js_handler['click']);
			} elseif (isset($js_handler['keypress'])) {
				$badge.= sprintf("<script> $('#%s_%s').keypress(%s); </script>", $formName, $this->getName(), $js_handler['keypress']);
			} elseif (isset($js_handler['keyup'])) {
				$badge.= sprintf("<script> $('#%s_%s').keyup(%s); </script>", $formName, $this->getName(), $js_handler['keyup']);
			} elseif (isset($js_handler['change'])) {
				$badge.= sprintf("<script> $('#%s_%s').change(%s); </script>", $formName, $this->getName(), $js_handler['change']);
			}
			return $badge;
		}
		return '';
	}

}
