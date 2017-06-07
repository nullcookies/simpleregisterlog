<?php

class nomvcPlainTextWidget extends nomvcBaseWidget {

	protected function init() {
		parent::init();
	}

	public function renderForForm($formName, $value = null) {
		return sprintf('<div id="form_group_%s" class="form-group"><label class="col-sm-offset-1 col-sm-10">%s</label></div>',
		$this->getName(), $this->getLabel());
	}


	public function renderForFilter($formName, $value = null) {
		return sprintf('<div id="form_group_%s" class="form-group"><label class="col-sm-offset-1 col-sm-10">%s</label></div>',
		$this->getName(), $this->getLabel());
	}
}
