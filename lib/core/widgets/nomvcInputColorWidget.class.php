<?php

class nomvcInputColorWidget extends nomvcInputWidget {

	protected function init() {
		parent::init();
		$this->setAttribute('type', 'text');
	}

	public function renderControl($value, $attributes = array()) {
		$attributes = array_merge($this->getAttributes(), $attributes);
		$attributes['class'] = implode(' ', array($attributes['class'], 'text-right'));
		if ($value) $attributes['value'] = '#'.str_pad(dechex($value), 6, '0');
		$attributesCompiled = $this->compileAttribute($attributes);
		return sprintf("<input %s></input><script>$('#%s').colorpicker();</script>",
			implode(' ', 	$attributesCompiled), $attributes['id'], $this->getOption('accuracy'));
	}
}
