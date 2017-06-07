<?php

class nomvcParametersListWidget extends nomvcBaseWidget {

	protected function init() {
		parent::init();
		$this->addOption('helper', true, false);
		$this->addOption('values', true, false);
		$this->addOption('relate', true, false);
		$this->addOption('collapse', false, true);
	}

	public function renderForForm($formName, $value = null) {
		$dbHelper = $this->getOption('helper');

		$valueOption = $this->getOption('values');

		($this->getOption('collapse')) ? $display = "none" : $display = "block";

		$panel_id = sprintf('checkboxes_table_%s_%s', $formName, $this->getName());

		$checkbox = new nomvcInputCheckboxWidget(null, null);
		$attributes = array(
			'name'			=> sprintf('%s[%s][set_all]', $formName, $this->getName()),
			'class'			=> 'checkbox-all',
			'target-panel'	=> $panel_id,
		);
		if ($this->getAttribute('readonly')) $attributes['readonly'] = 'readonly';
		if ($this->getAttribute('readonly')) $attributes['disabled'] = 'disabled';

		$header = array($checkbox->renderControl(null, $attributes));

		foreach ($valueOption['fields'] as $key => $name) {
			$header[] = $key;
			$fields[] = $name;
		}
		$header = '<tr><th>'.implode('</th><th>', $header).'</th></tr>';

		$sql = sprintf('select %s, %s from %s order by %s',
			$valueOption['key'], implode(', ', $fields),
			$valueOption['table'], isset($valueOption['order']) ? $valueOption['order'] : '2');
		$dbHelper->addQuery('select_for_widget/'.$valueOption['table'], $sql);
		$stmt = $dbHelper->select('select_for_widget/'.$valueOption['table']);

		if (!is_array($value)) $value = array();
		$rows = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$row[0] = strtolower($row[0]);
			$attributes = array(
				'name'			=> sprintf('%s[%s][%s][on]', $formName, $this->getName(), $row[0]),
				'class'			=> 'checkbox-element',
				'target-panel'	=> $panel_id,
			);
//			var_dump(array_key_exists($row[0], $value), $row[0],$value);
			$input_value = "";
			if (array_key_exists($row[0], $value)) {
				$attributes['checked'] = 'checked';
				$input_value = $value[$row[0]]["val"];
			}

			if ($this->getAttribute('readonly')) $attributes['readonly'] = 'readonly';
			if ($this->getAttribute('readonly')) $attributes['disabled'] = 'disabled';

			$param_id = sprintf('param_val_%s', $row[0]);
			$param_name = sprintf('%s[%s][%s][val]', $formName, $this->getName(), $row[0]);
			$input = sprintf('<input type="text" id="%s" name="%s" class="parameter-input" value="%s"></td>', $param_id, $param_name, $input_value);
			$row[0] = $checkbox->renderControl($row[0], $attributes);
			foreach ($row as $key1 => $value1) {
				if($value1 == "paramzzz"){
					$row[$key1] = $input;
				}
			}
			$rows[] = implode('</td><td>', $row);
		}
		$rows = '<tr><td>'.implode('</tr><tr><td>', $rows).'</tr>';
		$table = sprintf('<table class="table table-hover">
			<thead>%s</thead>
			<tbody>%s</tbody>
			</table>', $header, $rows);

		return sprintf(<<<EOF
<div id="form_group_%s" class="%s">
	<div class="panel panel-default" id="%s">
		<div class="panel-heading">
			<h3 class="panel-title">%s <span class="badge">0 / 0</span></h3>
		</div>
		<div class="panel-body pre-scrollable" style="display: $display;">%s</div>

		<script>
			$('#%s tbody.rowlink').rowlink({ target: '.checkbox-element' });
			$('#%s .panel-heading').click(ParametersList.expandPanel);
			$('#%s .checkbox-all').change(ParametersList.clickSelectAll);
			$('#%s .checkbox-element').change(ParametersList.checkStatus);
			$('#%s .parameter-input').change(ParametersList.checkParameter);
			CheckboxFromDb.firstCheckStatus('%s');
		</script>

	</div>
</div>

EOF
			, $this->getName(),
			$this->getOption('has-error', false) ? 'has-error' : '',
			$panel_id, $this->getLabel(), $table,
				$panel_id, $panel_id, $panel_id, $panel_id, $panel_id, $panel_id);
	}

	protected function buildRows($formName, $stmt, $panel_id, $value) {

	}

}
