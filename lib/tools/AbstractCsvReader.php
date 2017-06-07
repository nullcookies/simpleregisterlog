<?php

abstract class AbstractCsvReader {

    const MAX_ROW_LENGTH = 3000;

    protected $file;
    protected $csv_delimeter;
    protected $csv_enclosure;

    public function getRow() {
        return fgetcsv($this->file, self::MAX_ROW_LENGTH, $this->csv_delimeter, $this->csv_enclosure);
    }

    public function close() {
        fclose($this->file);
    }

    /**
     * $row - строка в которой ищем столбцы
     * $fields - список полей
     * $requiredFields - список обязательных полей
     * $isAll - определять все поля
     */
    public function getColumns($row, $fields) {
        $columns = array();
        // ищем определенные столбцы
        foreach ($fields as $name => $field) {
            $search_array = array($field['name']);
            if (isset($field['aliases'])) {
                $search_array = array_merge($search_array, $field['aliases']);
            }
            $search_result = array_keys(array_uintersect($row, $search_array, array('self', 'compareFields')));
            if (count($search_result)) $columns[$search_result[0]] = $name;
        }

        return $columns;
    }

    public static function compareFields($v1, $v2) {
        $v1 = trim(mb_strtolower($v1, 'UTF-8'));
        $v2 = trim(mb_strtolower($v2, 'UTF-8'));
        if ($v1 == $v2) return 0;
        if ($v1 > $v2) return 1;
        return -1;
    }

}
