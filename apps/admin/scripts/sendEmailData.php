<?php
    $fl = fopen('sendEmailData.lock', 'w+');
    if (!flock($fl, LOCK_EX + LOCK_NB)) { echo "locked!\r\n"; exit(); }

    define('NOMVC_APPNAME', 'admin');
    define('NOMVC_BASEDIR', dirname(dirname(dirname(dirname(__FILE__)))));

    require_once(NOMVC_BASEDIR.'/lib/autoload.php');

    try {
        $context = new Context(Context::ENV_DEBUG);
        $task = new SendEmailDataTask($context, null);
        $task->exec(array());
    } catch (Exception $ex) {
        echo $ex->getMessage();
    }
