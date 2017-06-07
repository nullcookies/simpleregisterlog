<?php
	define('API_GENERATOR_DIR', dirname(__DIR__).'/api/apigenerator');
	require_once(dirname(__FILE__).'/lib/autoload.php');

	try {
		$context = new ApiContext(agContext::ENV_PROD);
		
		$subject = 'Запрос от Unisender ' . date('Y-m-d H:i:s');
		$subject = mb_convert_encoding($subject, 'cp1251');

		$from = 'info@be-interactive.ru';

		$mail = new HtmlMimeMail();
		$body = print_r($_POST);

		$body = mb_convert_encoding($body, 'cp1251');

		$mail->send(
			null, 
			'a.pshenichnikov@be-interactive.ru', 
			null, 
			null, 
			$from, 
			$subject, 
			$body
		);
		
		echo "Test";
	
	} catch (Exception $ex) {
		echo $ex->getMessage();
	}