<?php

class nomvcActualResultsThreeWidget extends nomvcInputFileWidget {

    protected function init() {
        $this->addOption('path-upload', false, false);
        parent::init();
    }

    public function renderForForm($formName, $value = null){
        //$this->setAttribute('id', 'fileupload');
        $this->setAttribute('class','fileinput');
        $id = sprintf('%s_%s', $formName, $this->getName());

        //$html = parent::renderForForm($formName);
        $html = '';
        $html .= '<div id="form_group_results" class="form-group">';
        $html .= '<div class="col-sm-12"  style="overflow-x: auto;">
                  <table id="'.$id.'_table" style="overflow-x: auto;" class="table table-responsive table-bordered table-hover panel-body">
                    <thead>
                        <tr style="color: #fff;">
                            <th class="column_restaurant">Ресторан</th>
                            <th class="column_actual_val">Фактический показатель</th>
                        </tr>
                    </thead>
                    <tbody>';
        foreach ($value as $row) {
            $html .= '<tr>
                            <td class="column_restaurant">'.$row['restaurant'].'</td>
                            <td class="column_actual_val">'.(float) $row['base_result_val'].'</td>
                       </tr>';
        }

        $html .= '</tbody></table></div>';

        $is_new = (new nomvcInputHiddenWidget(null, $this->getName().'_is_new'))->renderForForm($formName);
        $js = "
        <script>
            var isNew = '$is_new';
            $('#$id').fileupload({
                url: '/admin.php/backend/' + '".$this->getOption('path-upload')."',
                autoUpload: true,
                success: function(data){
                    if (data == 'INCORRECT FILE')
                        alert('Некорректный файл!');
                    else {
                        var table = $(data).find('#".$id."_table').html();
                        if (table != ''){
                            $('#".$formName."').append(isNew);
                            $('#".$id."_table').html(table);
                        }
                    }
                }
            });
        </script>
        ";
        return $html.$js;
    }
}