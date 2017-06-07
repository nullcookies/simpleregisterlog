<?php
class PHPExcelAddon {

    public function __construct ($file) {
        require_once (dirname(__FILE__).'/../external/xls/PHPExcel.php');
        $this->objPHPExcel = PHPExcel_IOFactory::load($file);
        $this->dir = dirname($file);
    }

    function getColorLevel($val){
        $color='ffffff';
        $val = (float) $val;

        if ($val<=60)
            $color='ff3300';
        if ($val>60 && $val<81)
            $color='ffffcc';
        if ($val>=81 && $val<=100)
            $color='ccffcc';

        return $color;
    }

    public function setActiveSheet($page){
        $this->objPHPExcel->setActiveSheetIndex($page);
        return true;
    }

    public function convert($page, $data = array ()) {
        $this->objPHPExcel->setActiveSheetIndex($page);
        $aSheet = $this->objPHPExcel->getActiveSheet();

        $index=1;
        // Получим итератор строки и пройдемся по нему циклом
        foreach($aSheet->getRowIterator() as $row){
            // Получим итератор ячеек текущей строки
            $cellIterator = $row->getCellIterator();
            // Пройдемся циклом по ячейкам строки
            foreach($cellIterator as $cell){
                // Берем значение ячейки
                $val = $cell->getValue();

                // Проверяем содержит ли ячейка указатель на динамические данные шаблона (то что внутри %%)
                // Если да, то обрабатываем и заносим инфу
                if (preg_match( "#^%([^%]+)%$#sei", $val, $match )) {
                    $column = $cell->getColumn ();
                    $row 	= $cell->getRow ();

                    $key_insert = $match['1'];

                    //FFFF73
                    //	print 'key '. $key_insert." \r\n";

                    // Если строка пишем как строку
                    if (isset ($data[$key_insert]) AND is_string ($data[$key_insert])) {
                        //	print 'set string value '.$data[$key_insert]." \r\n";
                        $cell->setValue ($data[$key_insert]);
                    }
                    // Заполняем строки дублируя стили
                    else if (isset ($data[$key_insert]) AND is_array ($data[$key_insert])) {
                        // взяли стиль ячейки

                        $crew_template = $aSheet->getStyle($column.$row);

                        foreach ($data[$key_insert] as $_colVal) {
                            if ($key_insert=='row.cnt_per'){
                                $color = $this->getColorLevel($_colVal);
                                //закраить ячейку
                                $styleArray = array(
                                    'fill' => array(
                                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                        'color' => array(
                                            'rgb' => $color,
                                        ),
                                    ),
                                    'alignment' => array (
                                        'horizontal' 	=> PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                        'vertical'   	=> PHPExcel_Style_Alignment::VERTICAL_CENTER,
                                        'rotation'   	=> 0,
                                        'wrap'       	=> true,
                                        'shrinkToFit'	=> false,
                                        'indent'	=> 5
                                    ),
                                    'borders' => array (
                                        'bottom'     => array(
                                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                                            'color' => array(
                                                '	rgb' => '808080'
                                            )
                                        ),
                                        'top'     => array(
                                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                                            'color' => array(
                                                'rgb' => '808080'
                                            )
                                        ),
                                        'left'     => array(
                                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                                            'color' => array(
                                                '	rgb' => '808080'
                                            )
                                        ),
                                        'right'     => array(
                                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                                            'color' => array(
                                                'rgb' => '808080'
                                            )
                                        )
                                    )
                                );
                                $crew_template = $aSheet->getStyle($column.$row)->applyFromArray($styleArray);
                            }

                            $aSheet->duplicateStyle($crew_template,$column.$row);

                            //	print 'set row value '.$column.$row.' = '.$_colVal." \r\n";
                            $this->objPHPExcel->getActiveSheet()->SetCellValue($column.$row, $_colVal);
                            $row = $row+1;
                        }

                    }
                    // Если цифра
                    else if (isset ($data[$key_insert]) AND is_numeric ($data[$key_insert])) {
                        print 'set number value '.$data[$key_insert]." \r\n";
                        // <hh user=TODO> может кому то потребовать указать тип ячейки как цифровой
                        $cell->setValue ($data[$key_insert]);
                    }
                    // Если любая другая
                    else if (isset ($data[$key_insert])) {
                        //print 'set value '.$data[$key_insert]." \r\n";
                        $this->objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                        $cell->setValue ($data[$key_insert]);
                    }
                    // Если данный ключ шаблона не передан в нашу заполнялку
                    else {
                        //print "key value is empty \r\n";
                    }

                }
            }
            $index++;
        }

        //автоматическая высота строк
        for ($i=1; $i<=$index; $i++){
            $aSheet->getRowDimension($i)->setRowHeight(-1);
        }

        return true;
    }

    public function convert2($page, $data = array()) {
        $this->objPHPExcel->setActiveSheetIndex($page);
        $aSheet = $this->objPHPExcel->getActiveSheet();

        foreach ($data as $id_row => $row){
            foreach ($row as $id_col => $val){
                $aSheet->setCellValueByColumnAndRow($id_col, $id_row, $val);
            }
        }

        return true;

        $row_index = 0;
        foreach ($aSheet->getRowIterator() as $row) {
            // Получим итератор ячеек текущей строки
            $cellIterator = $row->getCellIterator();

            $row->
            // Пройдемся циклом по ячейкам строки

            $col_index = 0;
            foreach ($cellIterator as $cell) {
                $val = $cell->getValue();
                $column = $cell->getColumn();
                $row = $cell->getRow();

                var_dump($row_index, $col_index, $data[$row_index][$col_index]);
                if (isset($data[$row_index][$col_index]) and is_string($data[$row_index][$col_index])) {
                    $cell->setValue($data[$row_index][$col_index]);
                } // Заполняем строки дублируя стили
                else if (isset($data[$row_index][$col_index]) AND is_array($data[$row_index][$col_index])) {
                    // взяли стиль ячейки
                    $crew_template = $aSheet->getStyle($column . $row);

                    foreach ($data[$row_index][$col_index] as $_colVal) {
                        if ($key_insert == 'row.cnt_per') {
                            $color = $this->getColorLevel($_colVal);
                            //закраить ячейку
                            $styleArray = array(
                                'fill' => array(
                                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                    'color' => array(
                                        'rgb' => $color,
                                    ),
                                ),
                                'alignment' => array(
                                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                                    'rotation' => 0,
                                    'wrap' => true,
                                    'shrinkToFit' => false,
                                    'indent' => 5
                                ),
                                'borders' => array(
                                    'bottom' => array(
                                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                                        'color' => array(
                                            '	rgb' => '808080'
                                        )
                                    ),
                                    'top' => array(
                                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                                        'color' => array(
                                            'rgb' => '808080'
                                        )
                                    ),
                                    'left' => array(
                                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                                        'color' => array(
                                            '	rgb' => '808080'
                                        )
                                    ),
                                    'right' => array(
                                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                                        'color' => array(
                                            'rgb' => '808080'
                                        )
                                    )
                                )
                            );
                            $crew_template = $aSheet->getStyle($column . $row)->applyFromArray($styleArray);
                        }

                        $aSheet->duplicateStyle($crew_template, $column . $row);

                        //	print 'set row value '.$column.$row.' = '.$_colVal." \r\n";
                        $this->objPHPExcel->getActiveSheet()->SetCellValue($column . $row, $_colVal);
                        $row = $row + 1;
                    }

                } // Если цифра
                else if (isset($data[$row_index][$col_index]) AND is_numeric($data[$row_index][$col_index])) {
                    print 'set number value ' . $data[$key_insert] . " \r\n";
                    // <hh user=TODO> может кому то потребовать указать тип ячейки как цифровой
                    $cell->setValue($data[$row_index][$col_index]);
                } // Если любая другая
                else if (isset($data[$row_index][$col_index])) {
                    //print 'set value '.$data[$key_insert]." \r\n";
                    $this->objPHPExcel->getActiveSheet()->getStyle('C' . $i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                    $cell->setValue($data[$row_index][$col_index]);
                } // Если данный ключ шаблона не передан в нашу заполнялку
                else {
                    //print "key value is empty \r\n";
                }

                $col_index++;
            }

            $row_index++;
        }

        //автоматическая высота строк
        for ($i = 1; $i <= $row_index; $i ++){
            $aSheet->getRowDimension($i)->setRowHeight(-1);
        }

        return true;
    }

    public function write($name, $report){
        //$prefix = $this->generate_string(8);
        $name = $name.'.xls';

        $objWriter = PHPExcel_IOFactory::createWriter($this->objPHPExcel, 'Excel5');
        $objWriter->save(NOMVC_BASEDIR.'/web/data/reports/'.$name);

        $this->objPHPExcel->disconnectWorksheets();
        unset($this->objPHPExcel);

        return $name;
    }

    private function generate_string($count){
        $arr = array('a','b','c','d','e','f',
            'g','h','i','j','k','l',
            'm','n','o','p','r','s',
            't','u','v','x','y','z',
            'A','B','C','D','E','F',
            'G','H','I','J','K','L',
            'M','N','O','P','R','S',
            'T','U','V','X','Y','Z',
            '1','2','3','4','5','6',
            '7','8','9','0');
        $pass = "";
        for($i = 0; $i < $count; $i++)
        {
            $index = rand(0, count($arr) - 1);
            $pass .= $arr[$index];
        }
        return $pass;
    }
}