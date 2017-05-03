<?php

class MemberImportForm extends nomvcAbstractForm {

    public function init() {
        parent::init();
        $this->setAttribute('enctype', 'multipart/form-data');
        $this->addWidget(new nomvcInputFileWidget('Файл с пользователями', 'file', array()));
    }
}
