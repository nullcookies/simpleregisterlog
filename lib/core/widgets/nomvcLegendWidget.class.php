<?php

class nomvcLegendWidget extends nomvcBaseWidget {

	protected function init() {
		parent::init();
	}

	public function renderForForm($formName, $value = null) {

		return sprintf('<div id="form_group_%s" class="form-group"></div>',
			$this->getName());
	}

	public function renderForFilter($formName, $value = null) {
		return sprintf('<div id="form_group_%s" class="form-group"><span class="label label-success">%s</span></div>',
			$this->getName(), $this->getLabel());
	}
}
