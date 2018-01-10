<?php

/* /srv/www/simpleregisterlog/lib/external/mailer/PHPMailer6/src */

require_once "./external/mailer/PHPMailer6/src/PHPMailer.php";
require_once "./external/mailer/PHPMailer6/src/SMTP.php";
require_once "./external/mailer/PHPMailer6/src/POP3.php";
require_once "./external/mailer/PHPMailer6/src/OAuth.php";
require_once "./external/mailer/PHPMailer6/src/Exception.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\POP3;
use PHPMailer\PHPMailer\OAuth;
use PHPMailer\PHPMailer\Exception;


function sendTomTailorCode($email, $code, $discount) {

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
<p>С ПРАЗДНИКАМ!</p>
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

?>
