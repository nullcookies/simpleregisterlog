<?php

include_once dirname(__DIR__).'/../../lib/Mailer.class.php';
$mail = new Mailer();
$mail->SetAddresee(true);
$mail->SendEMail();