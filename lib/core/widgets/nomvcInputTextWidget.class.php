<?php

class nomvcInputTextWidget extends nomvcInputWidget {

	protected function init() {
		parent::init();
		$this->setAttribute('type', 'text');
	}

}
