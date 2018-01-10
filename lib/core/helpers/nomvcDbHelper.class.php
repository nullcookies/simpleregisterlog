<?php

/**
 * Хелпер базы данных, для упрощения доступа к БД
 */
abstract class nomvcDbHelper {

    protected $queries = array();
    protected $stmts = array();

    // ссылка на контекст
    protected $context;

    /** Конструктор */
    public function __construct($context) {
        $this->context = $context;
    }

    /**
     * Регистрация запроса в хелпере
     */
    public function addQuery($query_code, $query_sql, $auto_prepare = false) {
        $this->queries[$query_code] = $query_sql;
        unset($this->stmts[$query_code]);
        if ($auto_prepare) {
            $this->getStmt($query_code);
        }
    }

    /**
     * Возвращает стейтмент по коду запроса
     */
    public function getStmt($query_code, $values = array(), $params = array(), $lobs = array()) {
        if (!isset($this->stmts[$query_code])) {
            $this->stmts[$query_code] = $this->context->getDb()->prepare($this->queries[$query_code]);
        }
        $stmt = $this->stmts[$query_code];
        $this->bindValues($stmt, $values);
        $this->bindParams($stmt, $params);
        $this->bindLOBs($stmt, $lobs);
        return $stmt;
    }

    /**
     * выполнение запроса без возвращения результата
     */
    public function execute($query_code, $values = array(), $params = array(), $lobs = array()) {
        $stmt = $this->getStmt($query_code);
        $this->bindValues($stmt, $values);
        $this->bindParams($stmt, $params);
        $this->bindLOBs($stmt, $lobs);
        $this->doExecute($stmt);
    }


    /**
     * Выполнение запроса и возвращение стейтмента для последующего фетчинга
     * @param string	$query_code	Текст запроса
     * @param array		$values		Массив значений
     * @param array		$params		Массив параметров
     * @param array		$lobs		Массив LOB-ов
     */
    public function select($query_code, $values = array(), $params = array(), $lobs = array()) {	    
        $stmt = $this->getStmt($query_code);
        $this->bindValues($stmt, $values);
        $this->bindParams($stmt, $params);
        $this->bindLOBs($stmt, $lobs);
        
        return $this->doExecute($stmt);
    }

    /**
     * Выполняет запрос и возвращает значения первой строки в массиве
     */
    public function selectRow($query_code, $values = array(), $fetch_mode = PDO::FETCH_BOTH) {
        $stmt = $this->getStmt($query_code);
        $this->bindValues($stmt, $values);
        $this->doExecute($stmt);
        $row = $stmt->fetch($fetch_mode);
        
        if (is_array($row)) {
            return array_change_key_case($row, CASE_LOWER);
        }
        return false;
    }

    /**
     * Выполняет запрос и возвращает значение первого столбца первой строки
     */
    public function selectValue($query_code, $values = array()) {
        $stmt = $this->getStmt($query_code);
        $this->bindValues($stmt, $values);
        $this->doExecute($stmt);
        list($value) = $stmt->fetch();
        return $value;
    }

    /**
     * Биндит значения переменных
     */
    protected function bindValues($stmt, $values) {
        foreach($values as $name => $value) {
            $stmt->bindValue($name, $value);
        }
    }

    /**
     * Биндит возвращаемые параметры
     */
    protected function bindParams($stmt, $params) {
        foreach($params as $name => &$param) {
            $stmt->bindParam($name, $param, PDO::PARAM_STR, 100);
        }
    }

    /**
     * Биндит LOBs-ы
     */
    protected function bindLOBs($stmt, $lobs) {
        foreach($lobs as $name => $lob) {
            $stmt->bindParam($name, $lob, PDO::PARAM_LOB);
        }
    }

    /**
     * Выполняет запрос и отлавливает ошибки
     */
    protected function doExecute($stmt) {
        $this->beginTransaction();
        
        if(!$stmt->execute()) {
            ob_start();
            $stmt->debugDumpParams();
            $debug = ob_get_clean();
            $this->rollback();
            
            throw new nomvcGlobalException(serialize($stmt->errorInfo())."\r\n".$debug);
        }
        $this->commit();
        
        return $stmt;
    }

    public function beginTransaction() {
        $this->context->getDb()->beginTransaction();
    }

    public function commit() {
        $this->context->getDb()->commit();
    }

    public function rollback() {
        $this->context->getDb()->rollback();
    }

}
