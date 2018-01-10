<?php

class TestAction extends AbstractAction {

    public function getTitle() {
        return 'Тесты';
    }

    public function init()
    {
        parent::init();

        $this->addParameter('id_service', new agIntegerValidator(array('required' => true)), 'ID Сервиса');
    }

    public function execute() {
        $conn = $this->context->getDb();
        $view_sql = $this->getViewSqlForService($this->getValue('id_service'));
        $stmt = $conn->prepare($view_sql);
        $stmt->execute();

        $data = array();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $data[] = $row;
        }

        var_dump($data); exit;
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
        $stmt->bindValue('id_service', $this->getValue('id_service'));
        $stmt->execute();

        $meta = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            foreach ($this->asStrictTypes($row) as $key => $val){
                $row[$key] = strtolower($val);
            }

            $meta[] = $row;
        }

        ////
        $select = '
            select 
            tl.`id_log`,
            tl.`id_service`,
            ts.`name` as service,
            tl.`dt`
        ';
        $from = '
            from `T_LOG` tl
            inner join `T_SERVICE` ts on tl.`id_service` = ts.`id_service`
        ';
        $where = '
            where tl.`id_service` = '.$id_service.'
        ';

        $orderBy = 'order by tl.`id_log` desc';

        foreach ($meta as $key => $val){
            switch($val['meta_type']){
                case 'int':
                    $select .= ', tlmi'.$val['id_meta_key'].'.meta_value as '.$val['meta_key'].'';
                    $from .= 'left join `T_LOG_META_INT` tlmi'.$val['id_meta_key'].' on tl.`id_log` = tlmi'.$val['id_meta_key'].'.`id_log`';
                    break;
                case 'time':
                    $select .= ', tlmt'.$val['id_meta_key'].'.meta_value as '.$val['meta_key'].'';
                    $from .= 'left join `T_LOG_META_TIME` tlmt'.$val['id_meta_key'].' on tl.`id_log` = tlmt'.$val['id_meta_key'].'.`id_log`';
                    break;
                case 'text':
                default:
                    $select .= ', tlmt'.$val['id_meta_key'].'.meta_value as '.$val['meta_key'].'';
                    $from .= 'left join `T_LOG_META_TEXT` tlmt'.$val['id_meta_key'].' on tl.`id_log` = tlmt'.$val['id_meta_key'].'.`id_log`';
                    break;
            }
        }

        $sql = $select.$from.$where;//.$orderBy;

        return $sql;
        //var_dump($sql); exit;

        $stmt = $conn->prepare($sql);
        $stmt->execute();

        var_dump($stmt->fetch(PDO::FETCH_ASSOC)); exit;

        return;
    }
}