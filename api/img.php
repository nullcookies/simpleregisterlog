<?php

    define('API_GENERATOR_DIR', dirname(__DIR__).'/apigenerator');
    require_once(dirname(__FILE__).'/lib/autoload.php');

    try {
        $context = new ApiContext(agContext::ENV_DEBUG);
        $controller = new ApiImgController($context);
        $context->setController($controller);
        echo $controller->exec();

    } catch (Exception $ex) {
        echo $ex->getMessage();
    }

?>