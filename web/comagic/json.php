<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errrs', 0);

define('API_GENERATOR_DIR', dirname(__DIR__).'/../api/apigenerator');
require_once(dirname(__FILE__).'/../../api/lib/autoload.php');

try {
	/*$body = "---------------------\r\n";
	$body .= date_format(new DateTime(), 'Y-m-d H:i:s') . "\r\n";
	$body .= print_r($_POST, 1) . "\r\n";
	$body .= print_r($_GET, 1);
	$body .= "Post data:\r\n";*/

	if ($_SERVER['REQUEST_METHOD'] != 'POST') {
		http_response_code(400);
		echo "Must be POST request";
		exit();
	}

	$body = "---------------------\r\n";
	$body .= date_format(new DateTime(), 'Y-m-d H:i:s') . "\r\n";
	$body .= print_r($_POST, 1) . "\r\n";
	$body .= print_r($_GET, 1);

	$fh = fopen('php://input', 'r');
	$postData = '';

	while ($line = fgets($fh)) {
		$body .= $line;
		$postData .= $line;
	}

	$postData = json_decode($postData, true);

	if (!$postData) {
		http_response_code(400);
		echo "POST data empty empty";
		exit();
	}
	
	if (!$postData['cp']) {
		http_response_code(400);
		echo "CP not found";
		exit();
	}
	
	if (!$postData['AFFICHE_W']) {
		http_response_code(400);
		echo "Coockie not found";
		exit();
	}

	$cp = $postData['cp'];
	$webo = $postData['AFFICHE_W'];

	//echo $webo;
	
	$body .= "\r\nPost data:\r\n";
	$body .= "CP: $cp\r\n";
	$body .= "Webo: $webo\r\n";

	$f = fopen('comagic.log', 'a');
	fputs($f, $body);
	fclose($f);

	$url = 
		"http://comagic.solution.weborama.fr/fcgi-bin/dispatch.fcgi?" . 
		"a.A=co&" . 
		"a.si=5450&" . 
		"a.cp=$cp&" . 
		"a.ct=a&" . 
		"da=1522073869&" . 
		"g.ru=&" . 
		"g.pu=simpleregisterlog";

	$headers = array(
		"Cookie: AFFICHE_W=$webo;"
    );
	
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLINFO_HEADER_OUT, true);
	curl_exec($ch);

	$info = curl_getinfo($ch);
	//print_r($info);
	//exit();

	if (!curl_errno($ch)) {
		switch ($http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE)) {
			case 200:  # OK
			  break;
			default:
				http_response_code(400);
				echo "Unexpected HTTP code from Weborama: $http_code";
				exit();
		}
	}
	
	curl_close($ch);

	echo "Done";


	$f = fopen('comagic.log', 'a');
	fputs($f, "Webo done\r\n");
	fclose($f);
} catch (Exception $ex) {
	echo $ex->getMessage();
}
