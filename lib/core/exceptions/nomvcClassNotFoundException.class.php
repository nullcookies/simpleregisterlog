<?php

/**
 * Эксепшен, приносящий печальную весть о ненайденном классе
 */
class nomvcClassNotFoundException extends nomvcBaseException {

	protected $className;

	public function __construct($className) {
		parent::__construct("Class \"$className\" not found");
		$this->className = $className;
	}
	
	public function getClassName() {
		return $this->className;
	}
}
