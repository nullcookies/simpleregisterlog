<?php

class nomvcInputDateTimePeriodPickerWidget extends nomvcInputDatePeriodPickerWidget {
	
	protected function init() {
		parent::init();
		$this->setAttribute('data-date-format', 'DD.MM.YYYY HH:mm');
	}
	
	public function getScript($id) {
		return sprintf(<<<EOF
<script type="text/javascript">
	$(function () {
		$('#%s').datetimepicker({ language: 'ru' });
		$('#%s').datetimepicker({ language: 'ru' });
		$('#%s').on('dp.change', function (e) {
			$('#%s').data('DateTimePicker').setMinDate(e.date);
		});
		$('#%s').on("dp.change",function (e) {
			$('#%s').data('DateTimePicker').setMaxDate(e.date);
		});
	});
</script>
EOF
		, $id.'_from', $id.'_to', $id.'_from', $id.'_to', $id.'_to', $id.'_from');
	}
	
	public function formatDate($value, $k) {
		if (isset($value[$k]) && $value[$k] > '') {
			return DateHelper::dateConvert(DateHelper::DBT_FORMAT, 
				DateHelper::longToShortDateFormat($this->getAttribute('data-date-format', DateHelper::HTMLT_FORMAT)), $value[$k]);
		}
		return '';
	}
	
}
