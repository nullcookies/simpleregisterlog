<?php

class nomvcSelectFromDbWidget extends nomvcInputWidget {

	protected function init() {	
		parent::init();
		$this->addOption('helper', true, false);
		$this->addOption('table', true, false);
		$this->addOption('key', false, false);
		$this->addOption('val', false, false);
		$this->addOption('order', false, false);
		$this->addOption('options', false, array());

        $this->addOption('field_group', false, false);
	}
	
	public function renderControl($value, $attributes = array())
    {
        $dbHelper = $this->getOption('helper');

        
        if ($this->getOption('field_group')) {
            $sql = sprintf('select %s, %s, %s from %s order by %s, %s',
                $this->getOption('key', $this->getName()),
                $this->getOption('val', 'name'),
                $this->getOption('field_group'),
                $this->getOption('table'),
                $this->getOption('field_group', $this->getOption('key', 'name') == $this->getOption('val', 'name') ? '1' : $this->getOption('val', 'name')),
                $this->getOption('order', $this->getOption('key', 'name') == $this->getOption('val', 'name') ? '1' : $this->getOption('val', 'name'))
            );
        } else{
            $sql = sprintf('select %s, %s from %s order by %s',
                $this->getOption('key', $this->getName()),
                $this->getOption('val', 'name'),
                $this->getOption('table'),
                $this->getOption('order', $this->getOption('key', 'name') == $this->getOption('val', 'name') ? '1' : $this->getOption('val', 'name'))
            );
        }
        
		$dbHelper->addQuery('select_for_widget/'.$this->getOption('table'), $sql);
		$stmt = $dbHelper->select('select_for_widget/'.$this->getOption('table'));		
		$attributes = array_merge($this->getAttributes(), $attributes);
		$attributesCompiled = $this->compileAttribute($attributes);
		return sprintf('<select %s>%s</select>', implode(' ', 	$attributesCompiled), $this->renderOptions($value, $stmt, $attributes));
	}
	
	protected function renderOptions($value, $stmt, $attributes) {
		if ($value == null) $value = $this->getOption('value');
		$options = array('<option></option>');
		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$options[] = sprintf('<option value="%s"%s>%s</option>',
				$row[0], $value == $row[0] ? ' selected="selected"' : '', $row[1]);
		}
		return implode('', $options);
	}
	
}
