<?php

class nomvcInputDateTimePickerWidget extends nomvcInputDatePickerWidget {
	
	protected function init() {
		parent::init();
		$this->setAttribute('data-date-format', 'DD.MM.YYYY HH:mm');
	}
	
	public function getScript($id) {
		return sprintf(<<<EOF
<script type="text/javascript">
	$(function () {
		$('#%s').datetimepicker({ language: 'ru' });
	});
</script>
EOF
		, $id);
	}
	
	public function formatDate($value) {
		if (isset($value) && $value > '') {
			return DateHelper::dateConvert(DateHelper::DBT_FORMAT, 
				DateHelper::longToShortDateFormat($this->getAttribute('data-date-format', DateHelper::HTMLT_FORMAT)), $value);
		}
		return '';
	}
	
}
