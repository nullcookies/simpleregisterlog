<?php

class MembersTable extends AbstractMapObjectTable {
	public function init($options = array()) {
		

		$options = array(
		    'sort_by' => 'id_member',
		    'sort_order' => 'asc',
		);
	
		parent::init($options);
		
		$this->setRowModelClass('Members');

		$this->addColumn('id_member',	'ID',				'string');
		$this->addColumn('msisdn',		'Номер телефона',	'msisdn');
		$this->addColumn('name',		'ФИО',			'string');
		$this->addColumn('dt_birth',	'Дата рождения',	'date', array('format' => DateHelper::HTMLD_FORMAT));
		$this->addColumn('sex',			'Пол',				'string');
		$this->addColumn('haschildren',	'Есть дети?',		'string');
		$this->addColumn('push_limit',	'Режим уведомлений','string');
		
		$this->setFilterForm(new MembersFilterForm($this->context));
	}
	
	public function msisdnFormatter($column, $row) {
		return preg_replace('/7(\d{3})(\d{3})(\d{2})(\d{2})/', '+7-$1-XXX-$3-$4', $row->get($column));
	}
}
