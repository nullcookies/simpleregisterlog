<?php

class nomvcSelectFromArrayWidget extends nomvcInputWidget {

	protected function init() {
		parent::init();
		$this->addOption('order', false, false);
		$this->addOption('options', false, array());
	}

	/**
	 * Рендерим контрол. В нашем случае SELECT
	 *
	 * @param type name description
	 */
	public function renderControl($value, $attributes = array()) {
		$attributes = array_merge($this->getAttributes(), $attributes);
		$attributesCompiled = $this->compileAttribute($attributes);


		$label = !empty($this->getLabel()) && !empty($attributes["id"]) ? $this->renderLabel($attributes["id"]) : "";
		
		return sprintf('%s<select %s>%s</select>', $label, implode(' ',$attributesCompiled), $this->renderOptions($value, $attributes));
	}

	protected function renderOptions($value, $attributes) {
		if ($value == null) $value = $this->getOption('value');
		foreach ($this->getOption('options') as $key => $option) {
			$options[] = sprintf('<option value="%s"%s>%s</option>', $key, $value == $key ? ' selected="selected"' : '', $option);
		}
		return implode('', $options);
	}

	public function renderLabel($id, $with_class = true) {
		$attributes = array('for' => $id);
		if ($with_class)
			$attributes['class'] = $this->genColumnClass($this->getOption('label-width')) . ' control-label';
		$attributesCompiled = $this->compileAttribute($attributes);
		return sprintf('<label %s>%s%s</label>', implode(' ', $attributesCompiled), $this->getLabel(), $this->renderInformer());
	}

}
