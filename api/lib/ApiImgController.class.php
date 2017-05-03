<?php

/**
 * Контроллер выдачи графичекого контента
 *
 */

class ApiImgController extends agAbstractWebController {

    // мэппинг "какбыпапок" в таблицы в БД
    private $objTableMap = array(
        "member" => "t_member_photo"
    );
    // мэппинг "какбы ID" в таблице в БД
    private $objIdMap = array(
        "member" => "member"
    );

    public function exec() {
        // проверяем "какбыпапку" изображения
        if (!isset($_GET['obj']) || !isset($this->objTableMap[$_GET['obj']])) {
            header("HTTP/1.1 404 Not Found");
            return 'File not found';
        }
        // ищем изображение в БД
        try {
            $stmt = $this->context->getDb()->prepare("select mime_type, file_bin from {$this->objTableMap[$_GET['obj']]} where id_{$this->objIdMap[$_GET['obj']]}_photo = :id_photo");
            $stmt->bindValue('id_photo', $_GET['id']);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row) {
                header("HTTP/1.1 404 Not Found");
                return 'File not found';
            }

            $mime_type = $row['mime_type'];
            $file_res = $row['file_bin'];
        
            //var_dump($file_res); exit;
            header("Content-Type: $mime_type");
            //header("Content-Length: $file_res");
            fpassthru($file_res);
        }
        catch(exception $e){}
        
        return '';
    }
}