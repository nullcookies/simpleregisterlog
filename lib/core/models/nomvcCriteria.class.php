<?php

class nomvcCriteria {

	protected $from;
	protected $limit;
	protected $offset;
	protected $order;
	protected $values = array();
	protected $wheres = array();
	protected $context = array();

	public function __construct($from = null) {
	
	}

	public function setLimit($limit) {
		$this->limit = $limit;
	}
	
	public function setOffset($offset) {
		$this->offset = $offset;
	}
	
	public function setOrderBy($order) {
		$this->order = $order;
	}
	
	
	public function getLimit() {
		return $this->limit;
	}
	
	public function getOffset() {
		return $this->offset;
	}
	
	public function getOrderBy() {
		return $this->order;
	}
	
	public function addWhere($where, $values) {
		$this->wheres[] = $where;
		$this->values = array_merge($this->values, $values);
	}
	
	public function getWhere() {
		if (count($this->wheres)) {
			return 'where ('.implode(') and (', $this->wheres).')';
		}
		return null;
	}
	
	public function getValues() {
		return $this->values;
	}
	
	public function addContext($name, $value) {
		$this->context[$name] = $value;
	}
	
	public function getContext() {
		return $this->context;
	}

}
