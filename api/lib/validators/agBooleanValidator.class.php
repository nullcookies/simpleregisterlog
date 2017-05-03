<?php


class agBooleanValidator extends agBaseValidator {

    private $options = array();

    protected function init() {
        parent::init();
       // $this->addOption('pre_trim',false, false);
    }

    public function clean($value) {
        //$value = parent::clean($value);
        
        //проверка на то, что на входе массив
        if (!is_bool($value)){
            if (!is_array($value) && !($value instanceof stdClass))
                throw new agInvalidValueException('Is not a boolean');
        }
        
        return $value;
    }

    public function __toString() {
        $params = array('Boolean тип');
        if ($this->getOption('required')) {
            $params[] = 'обязательный';
        } else {
            $params[] = 'не обязательный';
        }

        return implode(', ', $params);
    }

    public function getExample() {
        return json_encode(true);
    }

}
