<?php

require_once dirname(dirname(__FILE__)) . '/extra/PHPMailer/src/PHPMailer.php';
require_once dirname(dirname(__FILE__)) . '/extra/PHPMailer/src/SMTP.php';
require_once dirname(dirname(__FILE__)) . '/extra/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


class SaveForTomTailorAction extends AbstractAction {

    public function getTitle() {
        return 'Сохранить в стиле Tom Tailor';
    }

    protected $id_service;

    public function init() {
        parent::init();

        $this->id_service = 32;

        $this->addParameter('id_code_type', new agIntegerValidator(array('required' => true)), 'ID Типа кода');

        $this->addParameter('email', new agStringValidator(array('required' => true)), 'Email');

        $this->registerActionException(Errors::CODE_TYPE_NOT_FOUND, 'Тип кода не найден');
        $this->registerActionException(Errors::CODE_TYPE_EMPTY, 'Коды данного типа закончились');

        $this->dbHelper->addQuery($this->getAction().'/check_exist_code_type', '
            select count(*) as cnt 
            from T_CODE_TYPE tct
            where tct.id_service = :id_service 
            and tct.id_code_type = :id_code_type
        ');

        $this->dbHelper->addQuery($this->getAction().'/get_id_code_types', '
            select tc.id_code_type
            from T_CODE tc
            inner join T_CODE_TYPE tct on tc.id_code_type = tct.id_code_type and tc.id_service = tct.id_service
            left join T_LOG tl on tc.code = tl.code and tl.id_service = tc.id_service
            where tl.id_log is null
            and tc.id_service = :id_service
            group by tc.id_code_type
        ');

        $this->dbHelper->addQuery($this->getAction().'/get_free_code', '
            select tc.code, tct.name
            from T_CODE tc
            inner join T_CODE_TYPE tct on tc.id_code_type = tct.id_code_type and tc.id_service = tct.id_service
            left join T_LOG tl on tc.code = tl.code and tl.id_service = tc.id_service
            where tl.id_log is null
            and tc.id_service = :id_service
            and tc.id_code_type = :id_code_type
            limit 1
        ');

        $this->dbHelper->addQuery($this->getAction().'/save_to_db_log', '
            insert into T_LOG (
                session_id,
                net,
                id_service,
                email,
                code,
                id_code_type
            ) values (
                :session_id,
                :net,
                :id_service, 
                :email,
                :code,
                :id_code_type
            )
        ');

        $this->dbHelper->addQuery($this->getAction().'/check_auto_email_notify', '
            select count(*) as cnt from T_SERVICE where id_service = :id_service and is_active = 1 and is_auto_email_notify = 1
        ');
    }

    public function execute() {

	//return array('result' => dirname(dirname(__FILE__)) . '/extra/PHPMailer/src/PHPMailer.php');

        if (!$this->dbHelper->selectValue($this->getAction().'/check_exist_code_type',  array(
            'id_service' => $this->id_service,
            'id_code_type' => $this->getValue('id_code_type')
        ))){
           $this->throwActionException(Errors::CODE_TYPE_NOT_FOUND);
        }

        $id_code_types = array();
        $stmt = $this->dbHelper->select($this->getAction() . '/get_id_code_types', [
            'id_service' => $this->id_service
        ]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $id_code_types[] = $this->asStrictType($row['id_code_type']);
        }

        if (!in_array($this->getValue('id_code_type'),$id_code_types)){
            $this->throwActionException(Errors::CODE_TYPE_EMPTY);
        }

        if ($row = $this->dbHelper->selectRow($this->getAction().'/get_free_code',  array(
            'id_service' => $this->id_service,
            'id_code_type' => $this->getValue('id_code_type')
        ))){
            $this->dbHelper->execute($this->getAction().'/save_to_db_log', array(
                'session_id' => session_id(),
                'net' => $this->getIp(),
                'id_service' => $this->id_service,
                'email' => $this->getValue('email'),
                'id_code_type' => $this->getValue('id_code_type'),
                'code' => $row['code']
            ));

            $this->sendCode($this->getValue('email'), $row['code'], $row['name']);

            return array('result' => Errors::SUCCESS);
        }

        return array('result' => Errors::FAIL);
    }
    /*
    protected function sendCode($email, $code, $discount){
        $mailSender = new EmailGlobalSender('TOM_TAILOR');
        $from = "\"TOM TAILOR\" <info@weborama.com.ru>";

        $subject = 'Ваша скидка в TOM TAILOR';

        $data[] = "Привет!";
        $data[] ="Поздравляем! Ваша скидка $discount";
        $data[] ="Код: ".$code;

        $string = "<p>".implode("</p><p>", $data)."</p>";
        $string = quoted_printable_encode($string);
        $email_string = file_get_contents(dirname(__FILE__).'/../mail.mht');
        $email_string = preg_replace("/\{emailtext\}/", $string, $email_string);

        try{
            $mail = $mailSender->newMail($from, $email, $subject, NULL);
            $mail->setMultipartEmailBody($email_string);
            $mail->send();
        }
        catch(exception $e){}

        return true;
    }
    */

    /**
     * @param $email
     * @param $code
     * @param $discount
     * @return bool
     */
    protected function sendCode($email, $code, $discount) {

        $mail = new PHPMailer(true);
	
        /*
         * HTML text
         */
        ob_start();
        ?>

<p>Привет!</p>
<p>Поздравляем! Ваша скидка <?php echo $discount; ?>:</p>
<p>Код: <?php echo $code; ?></p>
<br>
<p>*Скидка действительна на одну вещь в магазинах TOM TAILOR по адресам:</p>
<br>
<p>ТРЦ Авеню Юго-западный, пр-т Вернадского, 86А</p>
<p>ТРЦ Афимолл Сити, Пресненская наб., 2</p>
<p>ТРК Вегас, Каширское ш., 24 км МКАД</p>
<p>ТРЦ Весна, 84 км МКАД, 3</p>
<p>ТЦ Европейский, пл. Киевского вокзала, 2</p>
<p>ТРЦ Ереван Плаза, ул. Большая Тульская, 13</p>
<p>ТРЦ Колумбус, ул. Кировоградская, 13А</p>
<p>МЕГА Белая Дача, 14 км МКАД</p>
<p>ТЦ Метрополис, Ленинградское ш., 16А, стр. 4</p>
<p>ТЦ Охотный ряд, Манежная пл-дь, 1, стр. 2</p>
<p>ТРЦ Океания, Москва, Славянский б-р, вл. 3</p>
<p>ТЦ Филион, Багратионовский пр-д, 5</p>
<p>ТЦ Щука, ул. Щукинская, 42</p>
<p>ТЦ РИО, Дмитровское шоссе, 163А</p>
<br>
<p>С ПРАЗДНИКОМ!</p>
<p>TOM TAILOR</p>

[Unsubscribe]
[WebVersion]

        <?php
        $html = ob_get_clean();
        
        /*
         * PLAN text
         */
        ob_start();
        ?>
	
Привет!
Поздравляем! Ваша скидка: $discount
Код: $code

*Скидка действительна на одну вещь в магазинах TOM TAILOR по адресам:

ТРЦ Авеню Юго-западный, пр-т Вернадского, 86А
ТРЦ Афимолл Сити, Пресненская наб., 2
ТРК Вегас, Каширское ш., 24 км МКАД
ТРЦ Весна, 84 км МКАД, 3
ТЦ Европейский, пл. Киевского вокзала, 2
ТРЦ Ереван Плаза, ул. Большая Тульская, 13
ТРЦ Колумбус, ул. Кировоградская, 13А
МЕГА Белая Дача, 14 км МКАД
ТЦ Метрополис, Ленинградское ш., 16А, стр. 4
ТЦ Охотный ряд, Манежная пл-дь, 1, стр. 2
ТРЦ Океания, Москва, Славянский б-р, вл. 3
ТЦ Филион, Багратионовский пр-д, 5
ТЦ Щука, ул. Щукинская, 42
ТЦ РИО, Дмитровское шоссе, 163А

С ПРАЗДНИКОМ!
TOM TAILOR

[Unsubscribe]

        <?php
	$text = ob_get_clean();
	
        try {
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->CharSet = 'utf-8';
            $mail->Host = 'integrationapi.net';
            $mail->SMTPAuth = true;
            $mail->Username = 'TOMTAILOR_ADV';
            $mail->Password = 'F7Ge3sbn';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
	
            $mail->setFrom('noreply.rt@tom-tailor-online.ru', 'TOM TAILOR');
            $mail->addAddress($email);
	
            $mail->isHTML(true);
            $mail->Subject = 'Ваша скидка в TOM TAILOR';
            $mail->Body    = $html;
            $mail->AltBody = $text;
	
            $mail->send();
	
        } catch (Exception $e) {}
        
        return true;
    }

    public function getResponseExample() {
        return json_decode('{"response": {"result": 100}}');
    }
}
