<?php

    define('API_GENERATOR_DIR', dirname(__DIR__).'/apigenerator');
    require_once(dirname(__FILE__).'/lib/autoload.php');
  
    try {
        $context = new ApiContext(agContext::ENV_DEBUG);
        $context->setUser(new agConfigUser($context));

        $controller = new FileUploadController($context);
        $context->setController($controller);
        $output = $controller->exec();
        echo $output;
    } catch (Exception $ex) {
        echo $ex->getMessage();
    }
?>