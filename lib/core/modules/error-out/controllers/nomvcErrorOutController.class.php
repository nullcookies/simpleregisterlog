<?php

class nomvcErrorOutController extends nomvcBaseController {

	protected function init() {
		$this->context->addViewAddon('bootstrap');
	}
	
	public function run() {
		echo $this->exception->getMessage();
	}
	
	/** Этим методом получаем эксепшен */
	public function setException($exception) { $this->exception = $exception; }

}
