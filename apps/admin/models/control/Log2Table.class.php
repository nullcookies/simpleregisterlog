<?php

class Log2Table extends AbstractMapObjectTable {

    protected $id_service;

    public function init($options = array()) {
        $options = array(
            'sort_by' => 'dt',
            'sort_order' => 'desc',
            'rowlink' => <<<EOF
<script>
    //$('.rowlink').rowlink({ target: '.field_id_log' });
    //$('.field_id_log').click(function () {
    //    TableFormActions.getForm('log', $(this).closest('tr').attr('row-id'));
    //});
</script>
EOF
        );

        $this->id_service = $this->context->getUser()->getAttribute('id_service');

        parent::init($options);

        $this->setRowModelClass('Log2');

        $this->addColumn('id_log', 'ID', 'string');
        $this->addColumn('dt', 'Дата', 'string');
        $this->addColumn('service', 'Сервис', 'string');
        $this->addColumn('name', 'Имя', 'string');
        $this->addColumn('surname', 'Фамилия', 'string');
        $this->addColumn('patronymic', 'Отчество', 'string');
        $this->addColumn('msisdn', 'Телефон', 'string');
        $this->addColumn('email', 'Email', 'string');
        $this->addColumn('question_id', 'ID Вопроса', 'integer');
        $this->addColumn('question', 'Вопрос', 'string');
        $this->addColumn('answer_id', 'ID Ответа', 'integer');
        $this->addColumn('answer_order_num', 'Порядковый номер ответа', 'integer');
        $this->addColumn('answer', 'Ответ', 'string');
        $this->addColumn('metro_line_id', 'ID линии', 'integer');
        $this->addColumn('metro_line', 'Нзвание линии', 'string');
        $this->addColumn('metro_station_id', 'ID Станции', 'integer');
        $this->addColumn('metro_station', 'Название станции', 'string');
        $this->addColumn('metro_station_order_num', 'Порядковый номер станции', 'integer');
        $this->addColumn('mail_email_id', 'Email id', 'string');
        $this->addColumn('mail_event_name', 'Название события', 'string');
        $this->addColumn('mail_event_time', 'Время события', 'string');
        $this->addColumn('mail_email', 'Email to', 'string');
        $this->addColumn('mail_status', 'Статус отправки', 'string');
        $this->addColumn('mail_status_group', 'Статус отправки группы', 'string');
        $this->addColumn('company_type', 'company_type', 'string');
        $this->addColumn('no_of_staff', 'no_of_staff', 'string');
        $this->addColumn('email_adress', 'email_adress', 'string');
        $this->addColumn('inet_phone_spend_PM', 'inet_phone_spend_PM', 'string');
        $this->addColumn('data_bkup_spend_PM', 'data_bkup_spend_PM', 'string');
        $this->addColumn('srv_manage_cost_PM', 'srv_manage_cost_PM', 'string');
        $this->addColumn('city', 'Город', 'string');
        $this->addColumn('code', 'Код', 'string');
        $this->addColumn('num', 'Номер', 'string');

        $this->withMemberShowFields();

        $this->setFilterForm(new Log2FilterForm($this->context));
    }

    public function getViewSqlForService($id_service){
        $conn = $this->context->getDb();

        $sql = '
            select 
            tssf.id_show_field,
            tsf.name,
            tsf.name_rus,
            tmk.id_meta_key,
            tmk.name as meta_key,
            tmk.meta_type
            from `T_SERVICE_SHOW_FIELD` tssf
            inner join `T_SHOW_FIELD` tsf on tssf.id_show_field = tsf.id_show_field
            inner join `T_META_KEY` tmk on tsf.id_meta_key = tmk.id_meta_key
            where tssf.`id_service` = :id_service
            order by tssf.order_num
        ';
        $stmt = $conn->prepare($sql);
        $stmt->bindValue('id_service', $id_service);
        $stmt->execute();

        $meta = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            foreach ($row as $key => $val){
                $row[$key] = strtolower($val);
            }

            $meta[] = $row;
        }

        $select = '
            select 
            tl.`id_log`,
            tl.`id_service`,
            ts.`name` as service,
            tl.`dt`
        ';
        $from = '
            from `T_LOG` tl
            inner join `T_SERVICE` ts on tl.`id_service` = ts.`id_service`';
        $where = '
            where tl.`id_service` = '.$id_service.'
        ';

        foreach ($meta as $key => $val){
            switch($val['meta_type']){
                case 'int':
                    $select .= ', tlmi'.$val['id_meta_key'].'.meta_value as '.$val['meta_key'].'';
                    $from .= '
                    left join `T_LOG_META_INT` tlmi'.$val['id_meta_key'].' on tl.`id_log` = tlmi'.$val['id_meta_key'].'.`id_log`';
                    break;
                case 'time':
                    $select .= ', tlmt'.$val['id_meta_key'].'.meta_value as '.$val['meta_key'].'';
                    $from .= '
                    left join `T_LOG_META_TIME` tlmt'.$val['id_meta_key'].' on tl.`id_log` = tlmt'.$val['id_meta_key'].'.`id_log`';
                    break;
                case 'text':
                default:
                    $select .= ', tlmt'.$val['id_meta_key'].'.meta_value as '.$val['meta_key'].'';
                    $from .= '
                    left join `T_LOG_META_TEXT` tlmt'.$val['id_meta_key'].' on tl.`id_log` = tlmt'.$val['id_meta_key'].'.`id_log` and tlmt'.$val['id_meta_key'].'.id_meta_key='.$val['id_meta_key'].'';
                    break;
            }
        }

        $sql = $select.$from.$where;//.$orderBy;

        return $sql;
        var_dump($sql); exit;
    }

    protected function withMemberShowFields(){
        $conn = $this->context->getDb();

        $role_list = array();
        foreach ($this->context->getUser()->getAttribute('roles') as $key => $role){
            $role_list[$key] = $role['role'];
        }

        if (!in_array('root', $role_list)){
            $show_fields = $this->getMemberShowFields();
            $exclude_list = array('id_log', 'dt', 'service');

            foreach ($this->columns as $key => $column){
                if (!in_array($key, $show_fields) && !in_array($key, $exclude_list)){
                    $this->removeColumn($key);
                }
            }
        }

        $sql = '
            select 
            tssf.id_show_field,
            tsf.name,
            tsf.name_rus,
            tmk.id_meta_key,
            tmk.name as meta_key,
            tmk.meta_type
            from `T_SERVICE_SHOW_FIELD` tssf
            inner join `T_SHOW_FIELD` tsf on tssf.id_show_field = tsf.id_show_field
            inner join `T_META_KEY` tmk on tsf.id_meta_key = tmk.id_meta_key
            where tssf.`id_service` = :id_service
            order by tssf.order_num
        ';
        $stmt = $conn->prepare($sql);
        $stmt->bindValue('id_service', $this->id_service);
        $stmt->execute();

        $fields = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $fields[] = $row['name'];
        }

        $this->columns_old = $this->columns;

        $unset = false;
        foreach ($this->columns as $key => $column){
            if ($unset)
                unset($this->columns[$key]);

            if ($key == 'service'){
                $unset = true;
            }
        }

        foreach ($fields as $field) {
            if (isset($this->columns_old[$field])) {
                $this->columns[$field] = $this->columns_old[$field];
            }
        }
    }

    protected function getMemberShowFields(){
        $show_fields = array();
        $conn = $this->context->getDb();

        $stmt = $conn->prepare('
            select tsf.`NAME` AS `show_field`
            from `T_SHOW_FIELD` tsf 
            inner join `T_MEMBER_SHOW_FIELD` tmsf on tsf.`ID_SHOW_FIELD` = tmsf.`ID_SHOW_FIELD`
            where tmsf.`ID_MEMBER` = :id_member
        ');

        $stmt->bindValue('id_member', $this->context->getUser()->getAttribute('id_member'));
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $show_fields[] = $row['show_field'];
        }

        return $show_fields;
    }

    protected function runAsHtml() {
        $generator = new OutputGenerator($this->context, $this->controller);
        $criteria = $this->getCriteria();	// формируем условия выборки
        // донастраиваем поля таблицы
        foreach ($this->columns as $column => $conf) {
            if (!isset($this->columns[$column]['options']['sort_link'])) {
                $this->columns[$column]['options']['sort_link'] = $this->getSortLink($column);
            }
            if (!isset($this->columns[$column]['options']['value_formatter'])) {
                $this->columns[$column]['options']['value_formatter'] = array($this, $this->columns[$column]['type'].'Formatter');
            }
        }

        // конфигурация вывода таблицы
        $tableOutputConf = array(
            'columns'	=> $this->columns,
            'content'	=> $this->context->getModelFactory()->select2($this->rowModelClass, $this->getViewSqlForService($this->id_service), $criteria, $this->fetchByClass),
            'filters'	=> $this->filterForm,
            'batch'		=> $this->batchActions,
        );
        // если нужен пейжер - добавляем и его
        if ($this->getOption('with_pager')) {
            $tableOutputConf['pager'] = $generator->prepare('component/pager', array(
                'rows'		=> $this->getTotalRows(),
                'limit'		=> $this->getLimit(),
                'offset'	=> $this->getOffset(),
            ))->run();
        }
        // если нужна строка "всего"
        if ($this->getOption('with_total')) {
            $this->getTotalRows();
            $tableOutputConf['total'] = $this->total;
            $tableOutputConf['total_name'] = $this->getOption('with_total');
            $rowModelClass = $this->rowModelClass;
            $tableOutputConf['totals_field'] = $rowModelClass::getTotal();
        }
        // добавляем специальный блок кода, для построчной обработки
        if ($this->getOption('rowlink')) {
            $tableOutputConf['rowlink'] = $this->getOption('rowlink');
        }
        // собственно рендерим
        return $generator->prepare($this->tableTemplate, $tableOutputConf)->run();
    }

    protected function getTotalRows() {
        if ($this->total_rows == null) {
            $criteria = $this->getCriteria();
            $this->total = $this->context->getModelFactory()->count2($this->rowModelClass, $this->getViewSqlForService($this->id_service), $criteria);
            $this->total_rows = $this->total->count;
            if ($this->getOffset() > $this->total_rows) {
                $this->setOffset(ceil($this->total_rows / $this->getLimit()));
            }
        }
        return $this->total_rows;
    }

    protected function runAsXls() {

        $rowModelClass = $this->rowModelClass;

        $filename = 'export_'.$this->controller->underscore($rowModelClass)."_".date('d-m-Y').".xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $excelDoc = new PHPExcel();
        $excelDoc->setActiveSheetIndex(0);

        $sheet = $excelDoc->getActiveSheet();
        $sheet->setTitle('Export data');

        $styleArray = array(
            'borders' => array(
                'left'          => array('style' => PHPExcel_Style_Border::BORDER_THIN, ),
                'right'         => array('style' => PHPExcel_Style_Border::BORDER_THIN, ),
                'bottom'        => array('style' => PHPExcel_Style_Border::BORDER_THIN, ),
                'top'           => array('style' => PHPExcel_Style_Border::BORDER_THIN, ),
                'vertical'      => array('style' => PHPExcel_Style_Border::BORDER_THIN, ),
                'horizontal'=> array('style' => PHPExcel_Style_Border::BORDER_THIN, ),
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array(
                    'argb' => 'FFCCCCCC',
                ),
            ),
        );

        $rowNum = 1; $colNum = 0;
        foreach($this->columns as $column => $column_conf) {
            if (!isset($this->columns[$column]['options']['value_formatter'])) {
                $this->columns[$column]['options']['value_formatter'] = array($this, $this->columns[$column]['type'].'Formatter');
            }
            $sheet->setCellValueByColumnAndRow($colNum++, $rowNum, $column_conf['label']);
        }
        $sheet->getStyle('A'.$rowNum.':'.PHPExcel_Cell::stringFromColumnIndex($colNum - 1).$rowNum)->applyFromArray($styleArray);

        $criteria = $this->getCriteria();	// формируем условия выборки
        $rows = $this->context->getModelFactory()->select2($this->rowModelClass, $this->getViewSqlForService($this->id_service), $criteria, $this->fetchByClass);
        foreach ($rows as $row) {
            $rowNum++;
            $colNum = 0;
            foreach($this->columns as $column => $column_conf) {
                $sheet->setCellValueByColumnAndRow($colNum++, $rowNum, $column_conf['options']['value_formatter']($column, $row));
            }
        }
        $fill = $styleArray['fill'];
        unset($styleArray['fill']);
        $sheet->getStyle('A2:'.PHPExcel_Cell::stringFromColumnIndex($colNum - 1).$rowNum)->applyFromArray($styleArray);

        if ($this->getOption('with_total')) {
            $this->getTotalRows();
            $rowNum++;
            $colNum = 0;
            $totals_field = $rowModelClass::getTotal();
            foreach($this->columns as $column => $column_conf) {
                if ($colNum == 0) {
                    $sheet->setCellValueByColumnAndRow($colNum++, $rowNum, $this->getOption('with_total'));
                } elseif (isset($totals_field[$column])) {
                    $sheet->setCellValueByColumnAndRow($colNum++, $rowNum, $column_conf['options']['value_formatter']($column, $this->total));
                } else {
                    $colNum++;
                }
            }
            $styleArray['fill'] = $fill;
            $sheet->getStyle('A'.$rowNum.':'.PHPExcel_Cell::stringFromColumnIndex($colNum - 1).$rowNum)->applyFromArray($styleArray);
        }

        $colNum = 0;
        foreach($this->columns as $column => $column_conf) {
            $sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($colNum))->setAutoSize(true);
            $colNum++;
        }

        $writer = PHPExcel_IOFactory::createWriter($excelDoc, 'Excel2007');
        $writer->setIncludeCharts(TRUE);
        $writer->save('php://output');
    }
}
