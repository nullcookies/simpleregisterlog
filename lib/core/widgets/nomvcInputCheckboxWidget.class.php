<?php

class nomvcInputCheckboxWidget extends nomvcInputWidget {

	protected function init() {
		parent::init();
		$this->setAttribute('type', 'checkbox');
		$this->setAttribute('class', 'checkbox');
		$this->setAttribute('placeholder', false);
	}
	
	public function renderForForm($formName, $value = null) {
		$id = sprintf('%s_%s', $formName, $this->getName());
		$name = sprintf('%s[%s]', $formName, $this->getName());
		if(!empty($value)){ $checked = "checked"; }
		else { $checked = ""; }
		
		return sprintf('<div id="form_group_%s" class="form-group" %s><div class="checkbox container-fluid" ><label>%s %s</label></div></div>%s',
			$this->getName(),
			$this->getOption("hidden", ""),
			$this->renderControl($value, array_merge(array(
				'id' => $id,
				'name' => $name,
			        $checked => $checked
			), $this->getAttributes())),
			$this->getLabel(),
			$this->getJSHandler($formName)
		);
	}

}
