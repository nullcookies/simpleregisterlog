<?php

class nomvcModelFactory {

	protected $context;

	public function __construct($context) {
		$this->context = $context;
	}

	public function setContext($criteria = null) {
		if ($criteria) {
			foreach ($criteria->getContext() as $name => $value) {
				if (is_array($value)){
					if(empty($value)){ $value = null; }
					else{ $value = "^(" . implode ("|", $value) . ")$"; }
				}

                $conn = $this->context->getDb();
                $stmt = $conn->prepare('call setParameter(:parameter, :value); end;');
                $stmt->bindValue('parameter', $name);
                $stmt->bindValue('value', $value);
                $stmt->execute();
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

    public function select2($model, $sql, $criteria = null, $fetchByClass = false) {
        $this->setContext($criteria);
        $sql = $this->makeQuery2($sql, $criteria);
        $dbHelper = $this->context->getDbHelper();
        $query_code = md5($sql);
        $dbHelper->addQuery($query_code, $sql);

        $services = array();
        if ($id_services = $this->context->getUser()->getAttribute('id_services')){
            foreach ($id_services as $key => $id_service){
                $services["id_service_meta_$key"] = $id_service;
            }
        }

        $stmt = $dbHelper->select($query_code, array_merge($criteria->getValues(), $services));

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

    public function count2($model, $sql, $criteria = null) {
        $this->setContext($criteria);

        $sql = $this->makeCountQuery2($model, $sql, $criteria);

        $dbHelper = $this->context->getDbHelper();

        $query_code = md5($sql);
        $dbHelper->addQuery($query_code, $sql);

        $services = array();
        if ($id_services = $this->context->getUser()->getAttribute('id_services')){
            foreach ($id_services as $key => $id_service){
                $services["id_service_meta_$key"] = $id_service;
            }
        }

        $stmt = $dbHelper->select($query_code, array_merge($criteria->getValues(), $services));

        $stmt->setFetchMode(PDO::FETCH_CLASS, $model);

        return $stmt->fetch(PDO::FETCH_CLASS);
    }

	public function makeQuery($model, $criteria) {
		$sql = "select {$model::getTableName()}.*
                from {$model::getTableName()}";
		
		if ($criteria->getWhere() != null) {
			$sql .= ' '.$criteria->getWhere();
		}

        if ($criteria->getOrderBy() != null){
		    $sql .= " order by {$criteria->getOrderBy()} ";
        }

		if ($limit = $criteria->getLimit()) {
			$sql .= " limit {$criteria->getLimit()} offset {$criteria->getOffset()};";
		}

		return $sql;
	}

    public function makeQuery2($sql, $criteria) {
        $sql = "select t0.*
                from (
                  $sql
                ) t0";

        if ($criteria->getWhere() != null) {
            $sql .= ' '.$criteria->getWhere();
        }

        if ($criteria->getOrderBy() != null){
            $sql .= " order by {$criteria->getOrderBy()} ";
        }

        if ($limit = $criteria->getLimit()) {
            $sql .= " limit {$criteria->getLimit()} offset {$criteria->getOffset()};";
        }

        return $sql;
    }

    public function makeQueryWithSort($model, $criteria) {
        $sql = "select {$model::getTableName()}.*
                from {$model::getTableName()}";

        if ($criteria->getWhere() != null) {
            $sql .= ' '.$criteria->getWhere();
        }

        if ($criteria->getOrderBy() != null){
            $sql .= " order by {$criteria->getOrderBy()} ";
        }

        if ($limit = $criteria->getLimit()) {
            $sql .= " limit {$criteria->getLimit()} offset {$criteria->getOffset()};";
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

    public function makeCountQuery2($model, $sql, $criteria) {
        $fields = array('count(*) as "count"');
        foreach ($model::getTotal() as $field => $function) {
            $fields[] = "{$function}($field) as {$field}";
        }
        $fields = implode(', ', $fields);
        $sql = "select $fields from ($sql) t0";
        if ($criteria == null) {
            return $sql;
        }
        if ($where = $criteria->getWhere()) {
            $sql.= ' '.$where;
        }
        return $sql;
    }

}
