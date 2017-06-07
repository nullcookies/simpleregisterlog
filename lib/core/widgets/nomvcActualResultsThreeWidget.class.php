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
                            <th class="column_actual_status">Статус</th>
                            <th class="column_actual_is_approve">Подтверждено?</th>
                        </tr>
                    </thead>
                    <tbody>';
        foreach ($value as $row) {
            $html .= '<tr>
                            <td class="column_restaurant">'.$row['restaurant'].'</td>
                            <td class="column_actual_status">'.$row['actual_result_status'].'</td>
                            <td class="column_actual_is_approve"><input name="'.$formName.'['.$this->getName().'_check]['.$row['id_actual_result'].']" class="result_check" type="checkbox" '.($row['actual_result_is_approve']==1?'checked':'').' value="'.$row['id_actual_result'].'"/></td>
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
            
            /*
            $('.result_check').click(function(){
                var el = $(this);
                $.get('/admin.php/backend/result-check/?id=' + el.val(), function(data){
                    var data = JSON.parse(data);  
                    var state = el.prop('checked');
                    
                    if (data.result == 'success'){
                        el.prop('checked', state);
                    }
                    else
                        el.prop('checked', !state);
                });
            });
            */
        </script>
        ";
        return $html.$js;
    }
}