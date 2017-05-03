<?php
/**
 * Description of MembersFilterForm
 *
 * @author sefimov
 */
class MembersFilterForm extends nomvcAbstractFilterForm{

	public function init() {
		parent::init();
		
		$this->addWidget(new nomvcInputHiddenWidget(null, "id_map"));
		$this->addValidator('id_map', new nomvcIntegerValidator());
		$this->addContextMap('id_map', 'this_id_map');
		
		$this->addWidget(new nomvcInputTextWidget("ID", "id_member"));
		$this->addWidget(new nomvcInputTextWidget("ФИО", "name"));
		$this->addWidget(new nomvcInputDatePeriodPickerWidget("Дата рождения", "dt_birth"));
		$this->addWidget(new nomvcSelectFromDbWidget('Пол', 'id_sex', array(
		    'helper' => $this->context->getDbHelper(),
		    'table' => 'V_SEX',
		)));
		$this->addWidget(new nomvcSelectFromDbWidget('Есть ли дети', 'has_children', array(
		    'helper' => $this->context->getDbHelper(),
		    'table' => 'V_YES_NO',
		    'key'	=> 'id_yes_no'
		)));
		$this->addWidget(new nomvcSelectFromDbWidget('Режим уведомлений', 'id_push_limit', array(
		    'helper' => $this->context->getDbHelper(),
		    'table' => 'V_PUSH_LIMIT'
		)));
		
		$this->addValidator('id_member', new nomvcIntegerValidator(array('required' => false)));
		$this->addValidator('name', new nomvcStringValidator(array('required' => false,'min' => 2, 'max' => 200)));
		$this->addValidator("dt_birth", new nomvcDatePeriodValidator());
		$this->addValidator('id_sex', new nomvcValueInDbValidator(array(
		    'helper' => $this->context->getDbHelper(),
		    'table' => 'V_SEX',
		    'key'	=> 'id_sex'
		)));
		$this->addValidator('has_children', new nomvcValueInDbValidator(array(
		    'helper' => $this->context->getDbHelper(),
		    'table' => 'V_YES_NO',
		    'key'	=> 'id_yes_no'
		)));
		$this->addValidator('id_push_limit', new nomvcValueInDbValidator(array(
		    'helper' => $this->context->getDbHelper(),
		    'table' => 'V_PUSH_LIMIT',
		    'key'	=> 'id_push_limit'
		)));
		

		$this->addButton('search');
		$this->addButton('reset');
		
	}

}
