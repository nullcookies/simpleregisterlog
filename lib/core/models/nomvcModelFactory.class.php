<?php

class nomvcModelFactory {
	
	// ссылка на контекст
	protected $context;

	/** Конструктор */
	public function __construct($context) {
		$this->context = $context;
	}
	
//	protected function setContext($criteria = null) {
	public function setContext($criteria = null) {
		if ($criteria) {
			$dbHelper = $this->context->getDbHelper();
			foreach ($criteria->getContext() as $name => $value) {
				if(is_array($value)){ 
					if(empty($value)){ $value = null; }
					else{ $value = "(^" . implode ("|", $value) . "$)"; }
				}
				$sql = "begin project_context.set_parameter('{$name}', :{$name}); end;";
				$query_code = md5($sql);
				$dbHelper->addQuery($query_code, $sql);
				$dbHelper->execute($query_code, array($name => $value));
			}
		}
	}
	
	public function select($model, $criteria = null, $fetchByClass = false) {
		$this->setContext($criteria);
		$sql = $this->makeQuery($model, $criteria);
		$dbHelper = $this->context->getDbHelper();
		$query_code = md5($sql);
		$dbHelper->addQuery($query_code, $sql);
		$stmt = $dbHelper->select($query_code, $criteria->getValues());
		$data = array();
		if ($fetchByClass) {
			while ($obj = $stmt->fetch(PDO::FETCH_CLASS | PDO::FETCH_CLASSTYPE)) $data[] = $obj;
		} else {
			$stmt->setFetchMode(PDO::FETCH_CLASS, $model);
			while ($obj = $stmt->fetch(PDO::FETCH_CLASS)) $data[] = $obj;
		}
		return $data;
	}
	
	public function count($model, $criteria = null) {
		$this->setContext($criteria);
		$sql = $this->makeCountQuery($model, $criteria);
		$dbHelper = $this->context->getDbHelper();
		$query_code = md5($sql);
		$dbHelper->addQuery($query_code, $sql);
		
		$stmt = $dbHelper->select($query_code, $criteria->getValues());
		$stmt->setFetchMode(PDO::FETCH_CLASS, $model);
		return $stmt->fetch(PDO::FETCH_CLASS);
	}
	
	public function makeQuery($model, $criteria) {
		$sql = "select {$model::getTableName()}.*, @rownum:=@rownum+1 as mf_rownumber from {$model::getTableName()}, (SELECT @rownum:=0) t0";
		if ($criteria == null) {
			return $sql;
		}
		if ($where = $criteria->getWhere()) {
			$sql.= ' '.$where;
		}

		//var_dump($criteria->getWhere()); exit;

		$sql .= " order by {$criteria->getOrderBy()}";

		if ($limit = $criteria->getLimit()) {
			$sql= "select * from ($sql) t1 where mf_rownumber between {$criteria->getOffset()} + 1 and {$criteria->getLimit()} + {$criteria->getOffset()}";
		}
		return $sql;
	}
	
	public function makeCountQuery($model, $criteria) {
		$fields = array('count(*) as "count"');
		foreach ($model::getTotal() as $field => $function) {
			$fields[] = "{$function}($field) as {$field}";
		}
		$fields = implode(', ', $fields);
		$sql = "select $fields from {$model::getTableName()}";
		if ($criteria == null) {
			return $sql;
		}
		if ($where = $criteria->getWhere()) {
			$sql.= ' '.$where;
		}
		return $sql;
	}

}
