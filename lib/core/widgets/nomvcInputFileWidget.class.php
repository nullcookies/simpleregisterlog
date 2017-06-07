<?php

class nomvcInputFileWidget extends nomvcInputWidget {

	protected function init() {
		parent::init();
		$this->setAttribute('type', 'file');
	}

}
