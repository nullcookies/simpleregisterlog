<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define('NOMVC_APPNAME', 'admin');
define('NOMVC_BASEDIR', dirname(dirname(dirname(dirname(__FILE__)))));

require_once(NOMVC_BASEDIR.'/lib/autoload.php');

$subject = 'Test ' . date('Y-m-d H:i:s');
$subject = mb_convert_encoding($subject, 'cp1251');

$from = 'info@be-interactive.ru';

$mail = new HtmlMimeMail();
$body = "Test";

$body = mb_convert_encoding($body, 'cp1251');

$mail->send(
	null, 
	'a.pshenichnikov@be-interactive.ru;dyartsev@ITS.JNJ.com',
	null, 
	null, 
	$from, 
	$subject, 
	$body
);	
