<?php

class nomvcSelectFromMultipleDbWidget extends nomvcSelectFromDbWidget {


    protected function init() {	
        parent::init();
        $this->addOption('with-add', false, false);
        $this->addOption('with-add-url', false, false);
        $this->addOption('required', false, true);
        $this->addOption('multiple', false, false);
    }
    
    public function renderControl($value, $attributes = array()) {
        if ($this->getOption('with-add')) {
            $javaScript = sprintf(<<<EOF
<script>

$('#%s').chosen({
    no_results_text: '<a href="#" onClick="return SelectFromMultipleDb.addElement(\'%s\', \'%s\');">%s</a>'
});	

$('#%s_chosen').keydown(function (event) {
    if (event.keyCode == 13) {
        return SelectFromMultipleDb.addElement('%s', '%s');
    }
});

</script>
EOF
, $attributes['id'], $attributes['id'], $this->getOption('with-add-url'), $this->getOption('with-add'), $attributes['id'], $attributes['id'], $this->getOption('with-add-url'));
        } else {
            $opt = '';
            if ($this->getOption('required') == false)
            {
                $opt = "{'allow_single_deselect': true}";
            }
            
            $javaScript = sprintf("<script>$('#%s').chosen($opt);</script>", $attributes['id']);
        }
    
        
        if ($this->getOption('multiple')){
            $need_attributes = array_merge($attributes, array(
                'multiple'	=> 'multiple',
                'class'		=> (isset($attributes['class']) ? $attributes['class'] : '').' chosen-select',
                'name'		=> $attributes['name'].'[]'
            ));
        }
        else {
            $need_attributes = array_merge($attributes, array(
                'class'		=> (isset($attributes['class']) ? $attributes['class'] : '').' chosen-select',
                'name'		=> $attributes['name']
            ));
        }
                
        return parent::renderControl($value, $need_attributes).$javaScript;
    }
    
    protected function renderOptions($value, $stmt, $attributes) {
        $options = array('<option></option>');
        if ($value == null) {
            $value = array();
        }
        if (is_string($value))
            $value = [$value];
        
        $group_label = '';
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            if ($this->getOption('field_group'))
            if ($group_label != $row[2]){
                $group_label = $row[2];
                $options[] = "<optgroup label='".$group_label."'>";
            }
            
            $options[] = sprintf('<option value="%s"%s>%s</option>', $row[0], array_search($row[0], $value) !==false ? ' selected="selected"' : '', $row[1]);
        }
        return implode('', $options);
    }
}
