<?php

class nomvcInputMoneyWidget extends nomvcInputWidget {

	protected function init() {
		parent::init();
		$this->addOption('accuracy', false, 2);
		$this->addOption('currency-widget', false, false);
		$this->setAttribute('type', 'text');
	}

	public function renderControl($value, $attributes = array()) {
		$attributes = array_merge($this->getAttributes(), $attributes);
		if ($this->getOption('currency-widget')) {
			$attributes['class'] = implode(' ', array($attributes['class'], 'text-right'));
			$attributes['style'] = 'width: 60%';
			if ($value) $attributes['value'] = $value;
			$attributesCompiled = $this->compileAttribute($attributes);
			
			$currWidget = $this->getOption('currency-widget')->renderControl(null, array(
		  		'class' => 'form-control input-group-addon',
		  		'style' => 'width: 40%',
		  		'id'	=> sprintf('%s_%s', $attributes['form-id'], $this->getOption('currency-widget')->getName()), 
		  		'name'	=> sprintf('%s[%s]', $attributes['form-id'], $this->getOption('currency-widget')->getName()),
		  	));
			
			return sprintf(<<<EOF
				<div class="input-group">
					<input %s></input>
					%s
					<script>$('#%s').numberMask({ type:'float', afterPoint:%s, defaultValueInput:'10.1', decimalMark:'.'});</script>
				</div>
EOF
			  , implode(' ', 	$attributesCompiled), $currWidget,
				$attributes['id'], $this->getOption('accuracy'));
				
			
		} else {
			$attributes['class'] = implode(' ', array($attributes['class'], 'text-right'));
			if ($value) $attributes['value'] = $value;
			$attributesCompiled = $this->compileAttribute($attributes);
			return sprintf("<input %s></input><script>$('#%s').numberMask({ type:'float', afterPoint:%s, defaultValueInput:'10.1', decimalMark:'.'});</script>",
				implode(' ', 	$attributesCompiled), $attributes['id'], $this->getOption('accuracy'));
		}
	}
}
