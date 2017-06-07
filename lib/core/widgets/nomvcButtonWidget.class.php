<?php

class nomvcButtonWidget extends nomvcBaseWidget {

	protected function init() {
		parent::init();
		$this->addOption('type', false, 'button');
		$this->addOption('icon', false, false);
		$this->addOption('delete-confirm-form-id', FALSE, FALSE);

		$this->setAttribute('class', 'btn btn-primary');
		$this->setAttribute('placeholder', $this->getLabel());
	}

	public function renderForForm($formName, $value = null) {
		$id = sprintf('%s_%s', $formName, $this->getName());
		$name = sprintf('%s[%s]', $formName, $this->getName());

		return sprintf('<div id="form_group_%s" class="form-group"><div class="col-sm-12 text-right">%s</div></div>',
			$this->getName(),
			$this->renderControl($value, array_merge(array(
				'id' => $id,
				'name' => $name
			), $this->getAttributes()))

		);
	}

	public function renderForFilter($formName, $value = null) {
		$id = sprintf('%s_%s', $formName, $this->getName());
		$name = sprintf('%s[%s]', $formName, $this->getName());

		return sprintf('<div id="form_group_%s" class="form-group pull-right">%s</div>',
			$this->getName(),
			$this->renderControl($value, array_merge(array(
				'id' => $id,
				'name' => $name
			), $this->getAttributes()))
		);
	}

	public function renderControl($value, $attributes = array()) {
		$attributes = array_merge($this->getAttributes(), $attributes);

		if ($value) $attributes['value'] = $value;
		$attributesCompiled = $this->compileAttribute($attributes);

		return sprintf('<button %s>%s</button>', implode(' ', $attributesCompiled), $this->getLabel());
	}

	public function getLabel() {
		if ($this->getOption('icon')) {
			return sprintf('<span class="glyphicon glyphicon-%s"></span>%s', $this->getOption('icon'), parent::getLabel());
		} else {
			return parent::getLabel();
		}
	}

}
