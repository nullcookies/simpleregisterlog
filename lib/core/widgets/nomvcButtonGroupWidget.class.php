<?php

class nomvcButtonGroupWidget extends nomvcInputWidget {

	protected function init() {	
		parent::init();
		$this->addOption('order', false, false);
		$this->addOption('options', false, array());
	}
	
	public function renderForFilter($formName, $value = null) {
		$id = sprintf('%s_%s', $formName, $this->getName());
		$name = sprintf('%s[%s]', $formName, $this->getName());
		
		return sprintf(<<<EOF
<div id="form_group_%s" class="form-group%s">
	<input type="hidden" id="%s" name="%s" value="%s">
	<div class="btn-group" role="group" aria-label="" for-id="%s">
		%s
	</div>
</div>
<script>
	$('#form_group_%s .btn-group button').click(function() {
		var btn = $(this);
		if (!btn.hasClass('active')) {
			$('#form_group_%s .btn-group button').removeClass('active');
			btn.addClass('active');
			$('#%s').val(btn.val());
			btn.closest('form').submit();
		}
	});
</script>
EOF
			, $this->getName(), $this->getOption('has-error', false) ? ' has-error' : '',
			$id, $name, $value, $id, $this->renderOptions($value, array_merge(array('form-id' => $formName), $this->getAttributes())),
			$this->getName(), $this->getName(), $id);
	}
		
	protected function renderOptions($value, $attributes) {
		if ($value == null) $value = $this->getOption('value');
		foreach ($this->getOption('options') as $key => $option) {
			$options[] = sprintf('<button type="button" value="%s" class="btn btn-default%s">%s</button>', $key, $value == $key ? ' active' : '', $option);
		}
		return implode('', $options);
	}
	
}
