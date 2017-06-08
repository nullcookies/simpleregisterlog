<?php

define('API_GENERATOR_DIR', dirname(__DIR__).'/../api/apigenerator');
require_once(dirname(__FILE__).'/../../api/lib/autoload.php');

try {
	$context = new ApiContext(agContext::ENV_PROD);

	$subject = 'Запрос от Unisender ' . date('Y-m-d H:i:s');
	$subject = mb_convert_encoding($subject, 'cp1251');

	$from = 'info@be-interactive.ru';

	$mail = new HtmlMimeMail();
	$body = "---------------------\r\n";
	$body .= date_format(new DateTime(), 'Y-m-d H:i:s') . "\r\n";
	$body .= print_r($_POST, 1) . "\r\n";
	$body .= print_r($_GET, 1) . "\r\n";
	$body .= "---------------------\r\n";

	$body = mb_convert_encoding($body, 'cp1251');

	$mail->send(
		null, 
		'a.pshenichnikov@be-interactive.ru;a.russkih@be-interactive.ru',
		null, 
		null, 
		$from, 
		$subject, 
		$body
	);
	
	$f = fopen('mail.log', 'a');
	fputs($f, $body);
	fclose($f);
	
	//echo $_GET['value'].  "\r\n";
	echo "Done\r\n";
} catch (Exception $ex) {
	echo $ex->getMessage();
}
