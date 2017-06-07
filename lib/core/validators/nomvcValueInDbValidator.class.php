<?php

/**
 * Валидатор строковых значений
 */
class nomvcValueInDbValidator extends nomvcBaseValidator {

	protected function init() {
		parent::init();
		$this->addOption('helper', true, false);
		$this->addOption('table', true, false);
		$this->addOption('key', true, false);
	}

	public function clean($value) {
		
		$value = parent::clean($value);
		
		if ($this->addOption('required') == false && $value == null) {
			return null;
		}
		
		$helper = $this->getOption('helper');
		
		$sql = sprintf('select count(*) from %s where %s = :%s', $this->getOption('table'),
			$this->getOption('key'), $this->getOption('key'));
		$helper->addQuery('select_for_validator/'.$this->getOption('table'), $sql);
		$cnt = $helper->selectValue('select_for_validator/'.$this->getOption('table'), array($this->getOption('key') => $value));
		if ($cnt == 0) {
			throw new nomvcInvalidValueException($value, 'invalid');
		}
		
		return (string) $value;
	}
	
}
