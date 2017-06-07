<?php

class nomvcInputHiddenWidget extends nomvcInputWidget {

	protected function init() {
		parent::init();
		$this->setAttribute('type', 'hidden');
	}
	
	public function renderForForm($formName, $value = null) {
		$id = sprintf('%s_%s', $formName, $this->getName());
		$name = sprintf('%s[%s]', $formName, $this->getName());
		
		return $this->renderControl($value, array_merge(array('id' => $id, 'name' => $name), $this->getAttributes()));
	}

}
