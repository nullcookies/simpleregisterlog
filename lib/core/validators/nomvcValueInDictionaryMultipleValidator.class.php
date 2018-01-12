<?php

/**
 * Валидатор строковых значений
 */
class nomvcValueInDictionaryMultipleValidator extends nomvcValueInDbValidator {

    protected function init(){
        parent::init();
        $this->addOption('property', true, false);
        $this->addOption('table', false, false);
        $this->addOption('key', false, false);
    }

    public function clean($values) {
        if ($values == null) {
            $values = array();
        }

        foreach ($values as $key => $value) {
            if ($key == 'set_all' && $value == 'on') {
                unset($values[$key]);
            } else {
                $values[$key] = $this->clean2($value);
            }
        }

        if (count($values) == 0 && $this->getOption('required')) {
            throw new nomvcInvalidValueException(null, 'required');
        }

        return $values;
    }

    public function clean2($value) {
        if ($this->addOption('required') == false && $value == null) {
            return null;
        }

        $helper = $this->getOption('helper');

        $sql = sprintf('select count(*) from `T_DICTIONARY` where property = \'%s\' and property_key = :property_value',
            $this->getOption('property')
        );

        $helper->addQuery('select_for_validator/'.$this->getOption('table'), $sql);
        $cnt = $helper->selectValue('select_for_validator/'.$this->getOption('table'), array('property_value' => $value));
        if ($cnt == 0) {
            throw new nomvcInvalidValueException($value, 'invalid');
        }

        return (string) $value;
    }
}
