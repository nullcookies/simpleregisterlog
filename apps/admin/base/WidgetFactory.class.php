<?php

class WidgetFactory extends nomvcWidgetFactory {
	
	protected $context;

	public function __construct($context) {
		$this->context = $context;
		$this->baseWidgetsConf = sfYaml::load($this->context->getDir('app_config').'/widgetFactory/base/widgets.yml');
		$this->baseWidgetsConf = $this->baseWidgetsConf['widgets'];
		$this->widgetsConf = sfYaml::load($this->context->getDir('app_config').'/widgetFactory/widgets.yml');
		$this->widgetsConf = $this->widgetsConf['widgets'];
		
		$this->baseValidatorsConf = sfYaml::load($this->context->getDir('app_config').'/widgetFactory/base/validators.yml');
		$this->baseValidatorsConf = $this->baseValidatorsConf['validators'];
		$this->validatorsConf = sfYaml::load($this->context->getDir('app_config').'/widgetFactory/validators.yml');
		$this->validatorsConf = $this->validatorsConf['validators'];
		
		$this->filtersConf = sfYaml::load($this->context->getDir('app_config').'/widgetFactory/filters.yml');
		$this->filtersConf = $this->filtersConf['filters'];
		
	}
	
	public function addWidgets($form) {
		$formType = ($form instanceof nomvcAbstractFilterForm ? 'filter' : 'form').'sConf';
		$formConf = $this->$formType;
		$widgets = $formConf[get_class($form)]['widgets'];
		foreach ($widgets as $widget) {
			$form->addWidget($this->getWidget($widget));
		}
	}
	
	public function getWidget($widget) {
		$widgetConf = $this->widgetsConf[$widget];
		$class = $widgetConf['class'];
		$label = $widgetConf['label'];
		$name = isset($widgetConf['name']) && $widgetConf['name'] != null ? $widgetConf['name'] : $widget;
		$baseWidgetConf = $this->baseWidgetsConf[$class];
		$options = isset($widgetConf['options']) && $widgetConf['options'] != null ? $widgetConf['options'] : array();
		$options = array_merge($baseWidgetConf['options'], $options);
		$options = $this->setPredefinedOptions($options, $widget);
		$attributes = isset($widgetConf['attributes']) && $widgetConf['attributes'] != null ? $widgetConf['attributes'] : array();
		return new $class($label, $name, $options, $attributes);
	}
	
	public function addValidators($form) {
		$formType = ($form instanceof nomvcAbstractFilterForm ? 'filter' : 'form').'sConf';
		$formConf = $this->$formType;
		$validators = $formConf[get_class($form)]['validators'];
		if ($validators == null) {
			$validators = $formConf[get_class($form)]['widgets'];
		}
		foreach ($validators as $validator) {
			$widgetConf = $this->widgetsConf[$validator];
			$name = isset($widgetConf['name']) && $widgetConf['name'] != null ? $widgetConf['name'] : $validator;
			$form->addValidator($name, $this->getValidator($validator));
		}
	}
	
	public function getValidator($validator) {
		$validatorConf = $this->validatorsConf[$validator];
		if ($validatorConf == null) {
			$widgetClass = $this->widgetsConf[$validator]['class'];
			$class = $this->baseWidgetsConf[$widgetClass]['validator']['class'];
			$validatorConf = array();
			$widgetConf = $this->widgetsConf[$validator];
			$baseWidgetConf = $this->baseWidgetsConf[$widgetClass];
			$options = isset($widgetConf['options']) && $widgetConf['options'] != null ? $widgetConf['options'] : array();
			$options = array_merge($baseWidgetConf['options'], $options);
			$options = array_intersect_key($options, array_flip($this->baseWidgetsConf[$widgetClass]['validator']['options']));
			$options = $this->setPredefinedOptions($options, $validator);
		} else {
			$class = $validatorConf['class'];
			$baseValidatorConf = $this->baseValidatorsConf[$class];
			$options = isset($validatorConf['options']) && $validatorConf['options'] != null ? $validatorConf['options'] : array();
			$options = array_merge($baseValidatorConf['options'], $options);
			$options = $this->setPredefinedOptions($options, $validator);
		}
		return new $class($options);
	}
	
	protected function setPredefinedOptions($options, $code) {
		foreach ($options as $key => $val) {
			if (preg_match('/^~~([\w\d_]+)~~$/', $val, $matches)) {
				switch($matches[1]) {
				case 'CONTEXT_DB_HELPER':	$options[$key] = $this->context->getDbHelper();	break;
				case 'DB_VIEW':				$options[$key] = preg_replace('/^(id_)/', 'v_', $code); break;
				case 'WIDGET_KEY':			$options[$key] = $code; break;
				default:
					var_dump($matches[1]);
					echo 'ERROR 1';
					exit();
				}
			} else {
				$options[$key] = $val;
			}
		}
		return $options;
	}

}
