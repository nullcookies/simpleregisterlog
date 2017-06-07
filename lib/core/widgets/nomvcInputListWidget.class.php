<?php

/**
 * Виджет создания списков телефонов, почт, сайтов и т.п.
 */
class nomvcInputListWidget extends nomvcBaseWidget {
	protected function init() {
		parent::init();
		$this->addOption('helper', true, false);
		$this->addOption('settings', true, false);
		$this->addOption('collapse', false, true);
	}

	public function renderForForm($formName, $value = null) {
		//опции
		$settings = $this->getOption('settings');

		($this->getOption('collapse')) ? $display = "none" : $display = "block";

		//ID панели
		$panel_id = "input_table_{$formName}_{$this->getName()}";

		//разбираем поля
		foreach ($settings['fields'] as $key => $field) {
			$header[] = $key;
			$fields[$field["name"]] = $field;
		}
		//заголовок
		$header = '<tr><th>'.implode('</th><th>', $header).'</th><th></th></tr>';

		//количество колонок в таблице это сумма кол-ва полей + кнопа УДАЛИТЬ - две колонки выводимые, а остальное сливаем
		$column_qnty = count($fields) + 1 - 2;

		if ($this->getAttribute("disabled") == "disabled") {
			$disabled = "disabled";
			$add_new_button = "";
		} else {
			$disabled = "";
			$add_new_button = '
			<tr>
				<td colspan="'. $column_qnty .'">
				<td><label class="control-label">Добавить</label></td>
				<td class="text-right"><button type="button" class="btn btn-success input-list-add-row" target-panel="'.$panel_id.'"><span class="glyphicon glyphicon-plus"></span></button></td>
			</tr>';
		}

		$rows = "";
		$i = 0;
		$row_value = array();
		//биндятся данные
		if (is_array($value) && count($value)) {
			foreach ($value as $i => $row_value){
				$i++;
				$rows .= $this->renderRow($fields, $formName, $i, $panel_id, $row_value);
			}
		}

		//последняя невидимая строка, нужна для вставки новой строки
		$i++;
		$rows .= $this->renderRow($fields, $formName, $i, $panel_id);

		$table = sprintf('<table class="table table-hover">
			<thead>%s</thead>
			<tbody>%s%s</tbody>
			</table>', $header, $rows, $add_new_button);

		return sprintf(<<<EOF
<div id="form_group_%s" class="%s">
	<div class="panel panel-default" id="%s">
		<div class="panel-heading">
			<h3 class="panel-title">%s <span class="badge">0 / 0</span></h3>
		</div>
		<div class="panel-body pre-scrollable" style="display: %s;">%s</div>
		<script>
			$('#%s .panel-heading').click(InputList.expandPanel);
			$('#%s').on('change', '.checkbox-element', InputList.checkStatus);
			$('#%s').on('click', '.input-list-delete-row', InputList.deleteRow);
			$('#%s .input-list-add-row').click(InputList.addRow);
			InputList.firstCheckStatus('%s');
		</script>
	</div>
</div>
EOF
			, $this->getName(), $this->getOption('has-error', false) ? 'has-error' : '',
			$panel_id,
			$this->getLabel(),
			$display, $table,
			$panel_id, $panel_id, $panel_id, $panel_id, $panel_id, $panel_id, $panel_id);
	}


	/**
	 * Рендерим строку, возможно с данными
	 *
	 * @param type $fields		поля строки
	 * @param type $row_value	description
	 */
	private function renderRow($fields, $formName, $row_num, $panel_id, $row_value = array()) {
		if(empty($fields) || !is_array($fields))
			throw new nomvcAttributeException("Не указаны ключ или поля для nomvcInputListWidget");

		$display = "";
		if(empty($row_value)) $display = ' class="new-input-list-row" style="display: none;"';
		$row = "<tr row-num='$row_num' $display>";
		//прописываем поле value для полей
		foreach ($fields as $key => $value) {
			$fields[$key]["value"] = "";
		}

		//собираем значения по полям
		foreach ($row_value as $key => $value) {
			if(array_key_exists($key, $fields)) $fields[$key]["value"] = $value;
		}

		//формируем поля списка
		foreach ($fields as $field) {
			switch ($field["type"]) {
				case "input":
					$row .= $this->renderInput($field["name"], $field["value"], $formName, $row_num);
					break;
				case "checkbox":
					$row .= $this->renderCheckbox($field["name"], $field["value"], $formName, $panel_id, $row_num);
					break;
			}
		}
		$row .= '
			<td class="text-right"><button row-num="'.$row_num.'" type="button" class="btn btn-danger input-list-delete-row" target-panel="'.$panel_id.'"><span class="glyphicon glyphicon-trash"></span></button></td>
		</tr>';

		return $row;
	}

	/**
	 * Возвращает рендеренный чекбокс
	 *
	 * @param string $field_name	название поля
	 * @param string $field_value	значение поля
	 * @param string $formName	название формы
	 * @param string $panel_id	ID панели в которой лежит контрол
	 * @param integer $row_num	номер строки
	 */
	private function renderCheckbox($field_name, $field_value, $formName, $panel_id, $row_num) {
		$panel_id = "input_table_{$formName}_{$this->getName()}";
		$value = 1;
		$checked = "";
		if (!empty($field_value)) {
			$checked = "checked";
			$value = 1;
		}

		return sprintf('<td><input %s class="checkbox-element" type="checkbox" id="%s_%s_%s_%s" name="%s[%s][%s][%s]" value="%s" target-panel="%s"></td>',
			$checked,
			$formName, $this->getName(), $field_name, $row_num,
			$formName, $this->getName(), $row_num, $field_name,
			$value,
			$panel_id
		);
	}

	/**
	 * Возвращает рендеренный input
	 *
	 * @param string $field_name	название поля
	 * @param string $field_value	значение поля
	 * @param string $formName	название формы
	 * @param integer $row_num	номер строки
	 */
	private function renderInput($field_name, $field_value,  $formName, $row_num) {
		return sprintf('<td><input type="text" id="%s_%s_%s_%s" name="%s[%s][%s][%s]" class="form-control parameter-input" value="%s"></td>',
			$formName, $this->getName(), $field_name, $row_num,
			$formName, $this->getName(), $row_num, $field_name,
			$field_value);
	}

}
