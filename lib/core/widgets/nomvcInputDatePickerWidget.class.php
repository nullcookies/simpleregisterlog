<?php

class nomvcInputDatePickerWidget extends nomvcInputWidget {
	
	protected function init() {
		parent::init();
		$this->setAttribute('type', 'text');
		$this->setAttribute('data-date-format', 'DD.MM.YYYY');
	}
	
	public function renderForForm($formName, $value = null) {
		$id = sprintf('%s_%s', $formName, $this->getName());
		$name = sprintf('%s[%s]', $formName, $this->getName());
		
		return sprintf('<div %s id="form_group_%s" class="form-group%s">%s<div class="%s">%s</div>
		%s</div>',
			$this->getOption("hidden", ""),
			$this->getName(),
			$this->getOption('has-error', false) ? ' has-error' : '',
			$this->renderLabel($id),
			$this->genColumnClass(12 - $this->getOption('label-width')),
			$this->renderControl($this->formatDate($value), array_merge(array(
				'id' => $id,
				'name' => $name
			), $this->getAttributes())),
			$this->getScript($id));
	}
	
	
	public function formatDate($value) {
		if (isset($value) && $value > '') {			
			return DateHelper::dateConvert(DateHelper::DBD_FORMAT, 
				DateHelper::longToShortDateFormat($this->getAttribute('data-date-format', DateHelper::HTMLD_FORMAT)), $value);
		}
		return '';
	}
	
	public function renderLabel($id, $with_class = true) {
		$attributes = array('for'	=> $id);
		if ($with_class) $attributes['class'] = $this->genColumnClass($this->getOption('label-width')).' control-label';
		$attributesCompiled = $this->compileAttribute($attributes);
		return sprintf('<label %s>%s%s</label>', implode(' ', $attributesCompiled), $this->getLabel(), $this->renderInformer());
	}
	
		
	public function renderControl($value, $attributes = array()) {
		$attributes = array_merge($this->getAttributes(), $attributes);
		if ($value) $attributes['value'] = $value;
		$attributesCompiled = $this->compileAttribute($attributes);
		return sprintf(<<<EOF
            <div class="input-group date" id="%s">
                %s<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
            </div>
EOF
		, $attributes['id'], sprintf('<input %s>', implode(' ', 	$attributesCompiled)));
	}
	
	public function getScript($id) {
		return sprintf(<<<EOF
<script type="text/javascript">
	$(function () {
		$('#%s').datetimepicker({ language: 'ru', pickTime: false, minDate:'1.1.1900' });
	});
</script>
EOF
		, $id);
	}
	
}
