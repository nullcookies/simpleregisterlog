<?php

/**
 * Валидатор массивов
 */
class nomvcArrayValidator extends nomvcBaseValidator {

    protected function init() {
        parent::init();
        $this->addOption('min', false, false);
        $this->addOption('max', false, false);
    }

    public function clean($value) {
        if (!$value) {
            $value = array();
        } elseif (!is_array($value)) {
            throw new nomvcInvalidValueException($value, 'invalid');
        }
        
        $min = $this->getOption('min');
        if ($min && count($value) < $min) {
            throw new nomvcInvalidValueException($value, 'min');
        }
        $max = $this->getOption('max');
        if ($max && count($value) > $max) {
            throw new nomvcInvalidValueException($value, 'max');
        };
        return $value;
    }

}
