<?php

/**
 * Тэги. Подтверждение удаления
 */
class MemberDeleteConfirmForm extends nomvcAbstractForm {
	public function init() {
		parent::init();

		//ID_TAG
		$this->addWidget(new nomvcInputHiddenWidget('id_member', 'id_member'));
		$this->addValidator('id_member', new nomvcIntegerValidator(array('required' => false)));

		$this->addWidget(new nomvcPlainTextWidget('Внимание. Эти действия необратимы!!!', 'form_text'));
	}

}
