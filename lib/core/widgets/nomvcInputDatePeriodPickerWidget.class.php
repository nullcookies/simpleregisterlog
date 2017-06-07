<?php

class nomvcInputDatePeriodPickerWidget extends nomvcInputWidget {
    
    protected function init() {
        parent::init();
        $this->setAttribute('type', 'text');
        $this->setAttribute('data-date-format', 'DD.MM.YYYY');
    }
    
    public function renderForFilter($formName, $value = null) {
        $id = sprintf('%s_%s', $formName, $this->getName());
        $name = sprintf('%s[%s]', $formName, $this->getName());
        
        return sprintf('<div id="form_group_%s" class="form-group%s">%s%s%s%s</div>',
            $this->getName(),
            $this->getOption('has-error', false) ? ' has-error' : '',
            $this->renderLabel($id, false),
            $this->renderControl($this->formatDate($value, 'from'), array_merge(array(
                'id' => $id.'_from',
                'name' => $name.'[from]'
            ), $this->getAttributes())),
            $this->renderControl($this->formatDate($value, 'to'), array_merge(array(
                'id' => $id.'_to',
                'name' => $name.'[to]'
            ), $this->getAttributes())),
            $this->getScript($id));
    }
    
    public function formatDate($value, $k) {
        if (isset($value[$k]) && $value[$k] > '') {			
            return DateHelper::dateConvert(DateHelper::DBD_FORMAT, 
                DateHelper::longToShortDateFormat($this->getAttribute('data-date-format', DateHelper::HTMLD_FORMAT)), $value[$k]);
        }
        return '';
    }
    
    public function renderLabel($id, $with_class = true) {
        $attributes = array('for'	=> $id);
        if ($with_class) $attributes['class'] = $this->genColumnClass(3).' control-label';
        $attributesCompiled = $this->compileAttribute($attributes);
        return sprintf('<label %s>%s</label>', implode(' ', $attributesCompiled), $this->getLabel());
    }
    
        
    public function renderControl($value, $attributes = array()) {
        $attributes = array_merge($this->getAttributes(), $attributes);
        if ($value) $attributes['value'] = $value;
        $attributesCompiled = $this->compileAttribute($attributes);
        return sprintf(<<<EOF
            <div class="input-group date" id="%s">
                %s<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
            </div>
EOF
        , $attributes['id'], sprintf('<input %s>', implode(' ', 	$attributesCompiled)));
    }
    
    public function getScript($id) {
        return sprintf(<<<EOF
<script type="text/javascript">
    $(function () {
        $('#%s').datetimepicker({ language: 'ru', pickTime: false, minDate:'1.1.1900' });
        $('#%s').datetimepicker({ language: 'ru', pickTime: false, minDate:'1.1.1900' });
        $('#%s').on('dp.change', function (e) {
            $('#%s').data('DateTimePicker').setMinDate(e.date);
        });
        $('#%s').on("dp.change",function (e) {
            $('#%s').data('DateTimePicker').setMaxDate(e.date);
        });
    });
</script>
EOF
        , $id.'_from', $id.'_to', $id.'_from', $id.'_to', $id.'_to', $id.'_from');
    }
    
}
