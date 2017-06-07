<?php

class nomvcSelectFromAjaxWidget extends nomvcInputWidget {

	protected function init() {	
		parent::init();
		$this->addOption('ajaxurl', true, false);
		$this->addOption('parent_id', true, false);
	}
	
	public function renderControl($value, $attributes = array()) {
		$attributes = array_merge($this->getAttributes(), $attributes);
		$attributes['class'] = implode(' ', array($attributes['class'], ''));
		$attributes['ajaxurl'] = $this->getOption('ajaxurl');
		$attributes['parent-id'] = sprintf('%s_%s', $attributes['form-id'], $this->getOption('parent_id'));
		$attributes['default-value'] = $value;
		$attributesCompiled = $this->compileAttribute($attributes);
		return sprintf("<select %s></select><script> SelectFromAjax.init($('#%s'));</script>", implode(' ', $attributesCompiled), $attributes['id']);
	}
	
}
