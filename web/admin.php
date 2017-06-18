<?php
    session_name('admin-panel');

    /** @const Название проекта */
    define('NOMVC_APPNAME', 'admin');

    /** @const Базовая папка проекта */
    define('NOMVC_BASEDIR', dirname(dirname(__FILE__)));
    
    ini_set('session.cookie_lifetime', 86400);
    ini_set('session.gc_maxlifetime', 86400);
    
    session_id();
    
    require_once(dirname(__DIR__).'/lib/autoload.php');
    
    try {
        $context = new Context(Context::ENV_PROD);
//        $context = new Context(Context::ENV_DEBUG);
        echo $context->getRootController()->run();
    } catch (Exception $ex) {
        echo $ex->getMessage();
    }
