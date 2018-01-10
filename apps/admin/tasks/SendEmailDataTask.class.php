<?php

class SendEmailDataTask extends nomvcBaseTask{
    protected function init(){
        $this->path_template = NOMVC_BASEDIR.'/web/data/';
        $this->conn = $this->context->getDb();
    }

    public function exec($params) {
        parent::exec($params);

        //выбираем доступные
        $sql = "SELECT * FROM V_REPORT_EVENT WHERE NOW()>NEXT_DT"; // NOW()>NEXT_DT
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $ids = array();
        $reports = array();

        while ($row = $stmt->fetch()){
            $reports[] = array(
                'id_service_report' => $row['ID_SERVICE_REPORT'],
                'email_subject' => $row['EMAIL_SUBJECT'],
                'email_from' => $row['EMAIL_FROM'],
                'name' => $row['NAME_SERVICE'],
                'id_service' => $row['ID_SERVICE'],
                'id_period' => $row['ID_PERIOD'],
                'dt_from'	=> $row['LAST_DT'],
                'dt_to' => $row['NEXT_DT']
            );
            $ids[$row['ID_SERVICE']] = $row['ID_SERVICE'];
        }
        //var_dump($reports); exit;

        //находим почтовые ящики
        $sql = '
            select T_SERVICE_EMAIL.id_service, email 
            from T_SERVICE_EMAIL
            inner join T_SERVICE on (T_SERVICE.ID_SERVICE = T_SERVICE_EMAIL.ID_SERVICE AND T_SERVICE.IS_ACTIVE = 1)
            where T_SERVICE.id_service = :id_service
            group by T_SERVICE_EMAIL.id_service, email
        ';
        $stmt = $this->conn->prepare($sql);

        $emails = array();

        foreach ($ids as $id){
            $stmt->bindValue(':id_service', $id, PDO::PARAM_INT);
            $stmt->execute();

            while ($row = $stmt->fetch()){
                $emails[$id][] = $row['email'];
            }
        }
        //var_dump($emails); exit;

        foreach ($reports as $report){
            $subject = null;
            $def_subjects = array(
                1	=>	'Еженедельный отчет',
                2	=>	'Ежемесячный отчет',
                3	=>	'Ежеквартальный отчет',
                4	=>	'Ежедневный отчет'
            );
            if (!empty($report['email_subject'])){
                $subject = $report['email_subject'].' по '.$report['name'].' за период с '.$report['dt_from'].' 00:00:00'.' по '.$report['dt_to'].' 00:00:00';
            }
            else {
                $subject = $def_subjects[$report['id_period']];
            }

            $report['email_subject'] = $subject;
            $file = $this->createReport($report);

            $from = null;
            $def_from = 'info@be-interactive.ru';
            if (!empty($report['email_from'])){
                $from = $report['email_from'];
            }
            else {
                $from = $def_from;
            }

            //рассылаем
            if (isset($emails[$report['id_service']]))
                foreach ($emails[$report['id_service']] as $email){
                    $this->sendReport($email, $from, mb_convert_encoding($subject, 'cp1251'), $file);
                }

            //пишем лог
            $this->writeLog($report, $file);
        }

        //$this->conn->commit();
    }

    protected function writeLog($report, $file){
        $sql = 'INSERT INTO T_REPORT (id_service_report, id_service, id_period, file, dt_sended, dt_from, dt_to) values(:id_service_report, :id_service, :id_period, :file, now(), :dt_from, :dt_to)';
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(':id_service_report',$report['id_service_report'],PDO::PARAM_INT);
        $stmt->bindValue(':id_service',$report['id_service'],PDO::PARAM_INT);
        $stmt->bindValue(':id_period',$report['id_period'],PDO::PARAM_INT);
        $stmt->bindValue(':file',$file,PDO::PARAM_STR);
        $stmt->bindValue(':dt_from',$report['dt_from'],PDO::PARAM_INT);
        $stmt->bindValue(':dt_to',$report['dt_to'],PDO::PARAM_INT);
        $stmt->execute();
    }

    function sendReport($to, $from, $subject, $file_name) {
        $mail = new HtmlMimeMail();
        $mail->add_attachment($this->path_template.'reports/', $file_name);
        $mail->build_message('qwerty');

        if ($mail->send(null, $to, null, null, $from, $subject)){
            echo "+1 отчет отправлен на $to\n";
        }
        else {
            echo "ошибка отправки на $to\n";
        };
    }

    protected function createReport($report){
        $this->doc = new PHPExcelAddon($this->path_template.'/template_base.xls');

        $this->createExcelDetail0($report);

        $this->doc->setActiveSheet(0);

        $file = $this->doc->write($report['email_subject'], $report);

        return 	$file;
    }

    protected function createExcelDetail0($report){
        $sql = '
            select 
            tsf.name as field, 
            tsf.name_rus as label
            from T_SERVICE_REPORT_FIELD tsrf
            inner join T_SHOW_FIELD tsf on tsrf.id_show_field = tsf.id_show_field
            where tsrf.id_service_report = :id_service_report
            order by tsrf.order_num
        ';
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_service_report', $report['id_service_report']);
        $stmt->execute();

        $fields = array();
        $i = 0;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $fields[$row['field']] = array(
                'label' => $row['label'],
                'order_num' => $i
            );
            $i++;
        }
        //var_dump($fields); exit;

        $sql = '
            SELECT 
            tl.*
            FROM T_LOG tl
            INNER JOIN T_SERVICE ts ON (tl.ID_SERVICE = ts.ID_SERVICE)
            INNER join T_SERVICE_REPORT_PERIOD tsrp on ts.id_service = tsrp.id_service
            WHERE tl.id_service = :id_service
            and tl.dt >= :dt_from 
            and tl.dt <= :dt_to
        ';

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_service', $report['id_service']);
        $stmt->bindValue(':dt_from', $report['dt_from']);
        $stmt->bindValue(':dt_to', $report['dt_to']);
        $stmt->execute();

        $data = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $tmp = array();
            foreach ($row as $key => $val) {
                $tmp[strtolower($key)] = $val;
            }

            $data[] = $tmp;
        }

        $result = array();
        foreach ($data as $id => $item){
            $tmp_item = array();
            foreach ($item as $key => $val) {
                if (key_exists($key, $fields)) {
                    $tmp_item[$fields[$key]['order_num']] = $val;
                }
            }
            ksort($tmp_item);
            $result[] = $tmp_item;
        }
        //var_dump($fields, $result); exit;

        $data_write = array();

        //header
        foreach ($fields as $field => $prop) {
            $headers[] = $prop['label'];
        }
        $data_write[1] = @$headers;
        //var_dump($data_write); exit;

        //body
        foreach ($result as $row) {
            $data_write[] = $row;
        }
        //var_dump($data_write); exit;

        $this->doc->convert2(0, $data_write);

        return true;
    }
}
