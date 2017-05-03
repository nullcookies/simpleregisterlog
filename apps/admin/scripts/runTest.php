<?php

	$fl = fopen(substr(__FILE__, 0, strlen(__FILE__) - 4).'.lock', 'w+');
	if (!flock($fl, LOCK_EX + LOCK_NB)) { echo "locked!\r\n"; exit(); }

	define('NOMVC_APPNAME', 'index');
	define('NOMVC_BASEDIR', dirname(dirname(dirname(dirname(__FILE__)))));
		
	require_once(NOMVC_BASEDIR.'/lib/autoload.php');
	
	try {
		$context = new Context(Context::ENV_DEBUG);
		$task = new SendApplePushTask($context, null);
		$task->exec(array());
	} catch (Exception $ex) {
		echo $ex->getMessage();
	}
