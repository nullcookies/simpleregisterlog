<?php

/**
 * Виджет создания списков телефонов, почт, сайтов и т.п.
 */
class nomvcOpeningTimeWidget extends nomvcBaseWidget {
	/** @var dbHelper Хелпер базы данных */
	private $dbHelper;

	protected function init() {
		parent::init();
		$this->addOption('helper', true, false);
		$this->addOption('collapse', false, true);
	}

	public function renderForForm($formName, $value = null) {
		//панель раскрыта или нет
		($this->getOption('collapse')) ? $display = "none" : $display = "block";

		//ID панели
		$panel_id = "opening_table_{$formName}_{$this->getName()}";

		if ($this->getAttribute("disabled") == "disabled") {
			$disabled = "disabled";
			$add_new_button = "";
		} else {
			$disabled = "";
			$add_new_button = '
			<tr>
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
				$rows .= $this->renderRow($formName, $i, $panel_id, $row_value);
			}
		}

		//последняя невидимая строка, нужна для вставки новой строки
		$i++;
		$rows .= $this->renderRow($formName, $i, $panel_id);

		$table = sprintf('<table class="table table-hover">
			<tbody>%s%s</tbody>
			</table>', $rows, $add_new_button);

		return sprintf(<<<EOF
<div id="form_group_%s" class="%s">
	<div class="panel panel-default" id="%s">
		<div class="panel-heading">
			<h3 class="panel-title">%s <span class="badge">0 / 0</span></h3>
		</div>
		<div class="panel-body pre-scrollable" style="display: %s;">%s</div>
		<script>
			$('#%s .panel-heading').click(InputList.expandPanel);
			$('#%s').on('click', '.input-list-delete-row', OpeningTime.deleteRow);
			$('#%s .input-list-add-row').click(OpeningTime.addRow);
			OpeningTime.firstCheckStatus('%s');
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
	 * @param type $formName	Название формы
	 * @param type $row_num		Номер строки
	 * @param type $panel_id	ID панели, нужно для изменения числа строк
	 * @param type $row_value	description
	 */
	private function renderRow($formName, $row_num, $panel_id, $row_value = array()) {
		$display = "";
		if(empty($row_value)) $display = ' class="new-input-list-row" style="display: none;"';
		$row = "<tr row-num='$row_num' $display><td colspan='2'>";

		//строим строчку селектов
		$fields = array(array("from_hour", 23, ""), array("from_min", 59, ":"), array("to_hour", 23, " &mdash; "), array("to_min", 59, ":"));
		foreach ($fields as $field) {
			$field_value = array_key_exists($field[0], $row_value)? $row_value[$field[0]] : NULL;
			$row .= '<div style="display: inline-block; width: 20%; text-align: center;">'.
				$this->renderSelect($formName, $row_num, $field[0], $field_value, $field[1], $field[2]).
			'</div>';
		}

		//кнопа удалить
		$row .= '<button style="float: right;" row-num="'.$row_num.'" type="button" class="btn btn-danger input-list-delete-row" target-panel="'.$panel_id.'"><span class="glyphicon glyphicon-trash"></span></button>';

		//строим строчку чекбоксов
		$this->dbHelper = $this->getOption("helper");
		$this->dbHelper->addQuery(get_class($this) . '/get-week-day', "select id_week_day, name, short_name from t_week_day order by order_by_week_day");
		$stmt = $this->dbHelper->select(get_class($this) . '/get-week-day', array());
		$day = array();
		$field_value = null;
		while ($day = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$day = array_change_key_case($day);
			if(!empty($row_value)) $field_value = array_key_exists($day["id_week_day"], $row_value["day"]);
			$row .= $this->renderCheckbox("[day][{$day["id_week_day"]}]", $field_value, $formName, $row_num, $day["short_name"]);
		}


		$row .= '</td></tr>';

		return $row;
	}


	/**
	 * Возвращает рендеренный чекбокс
	 *
	 * @param string $field_name	название поля
	 * @param string $field_value	значение поля
	 * @param string $formName	название формы
	 * @param integer $row_num	номер строки
	 * @param integer $label	лэйбл чекбокса
	 */
	private function renderCheckbox($field_name, $field_value, $formName, $row_num, $label){
		$panel_id = "opening_table_{$formName}_{$this->getName()}";
		$control_id = "{$formName}_{$this->getName()}_{$row_num}_{$field_name}";
		$checked = "";
		if (!empty($field_value)) {
			$checked = "checked";
		}

		$control = sprintf('<input %s class="checkbox-element" type="checkbox" id="%s" name="%s[%s][%s]%s" value="1"><br>
				<label for="%s">%s</label>
			</div>',
			$checked, $control_id, $formName, $this->getName(), $row_num, $field_name,
			$control_id, $label
		);

		$control = '<div style="display: inline-block; width: 13%; text-align: center;">'.$control;
		return $control;
	}


	/**
	 * Возвращает рендеренный селект
	 * @param string $formName	Название формы
	 * @param int	 $row_num	Номер строки
	 * @param string $field_name	Название поля
	 * @param string $field_value	Значение поля
	 * @param string $label		Лэйбл
	 */
	private function renderSelect($formName, $row_num, $field_name, $field_value = NULL, $value_limit = 23, $label = "") {
		$field_id = "{$formName}_{$this->getName()}_{$row_num}_{$field_name}";
		$field_name = $formName."[{$this->getName()}][$row_num][$field_name]";
		$select = new nomvcSelectFromArrayWidget($label, $field_name,
			array("options" => $this->generateSequenceArray($value_limit)),
			array("style" => "display: inline-block; width: auto;"));
		return $select->renderControl($field_value, array("id" => $field_id, "name" => $field_name));
	}

	/**
	 * Генерирует массивы часов, минут или секунд
	 * @param int $limit Число на котором надо остановиться
	 */
	private function generateSequenceArray($limit = 0) {
		$sequence_array = array();
		$index = 0;
		//если это минуты - итерация каждые 15 минут
		$itteration = $limit == 59 ? 15 : 1;
		while ($index <= $limit){
			$sequence_array[$index] = $index <= 9 ? "0$index" : "$index";
			$index += $itteration;
		}

		return $sequence_array;
	}

}
