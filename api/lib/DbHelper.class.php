<?php

class DbHelper extends agAbstractDbHelper {

    protected function init() {

    }

    public function generateId($sequence) {
        $stmt = $this->context->getDb()->prepare("select {$sequence}.nextval from dual");
        list($value) = $this->doExecute($stmt)->fetch();
        $stmt->closeCursor();

        $value = $this->asStrictType($value);
        
        return $value;
    }

    public function selectValue($query_code, $values = array(), $type = false) {
        $stmt = $this->getStmt($query_code);
        $this->bindValues($stmt, $values);
        $this->doExecute($stmt);
        list($value) = $stmt->fetch();
        $stmt->closeCursor();

        $value = $this->asStrictType($value, $type);
        
        return $value;
    }

    public function selectRow($query_code, $values = array(), $fetch = PDO::FETCH_ASSOC, $types = array()) {
        $stmt = $this->getStmt($query_code);
        $this->bindValues($stmt, $values);
        $this->doExecute($stmt);
        $row = $stmt->fetch($fetch);
        $stmt->closeCursor();

        $row = $this->asStrictTypes($row, $types);
        
        //var_dump($row); exit;
        return $row;
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
            throw new agGlobalException(serialize($stmt->errorInfo())."\r\n".$debug);
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

?>
