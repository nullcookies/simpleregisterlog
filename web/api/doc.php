<?php
define('API_GENERATOR_DIR', dirname(__DIR__).'/../api/apigenerator');
require_once(dirname(__FILE__).'/../../api/lib/autoload.php');

try {
    $context = new ApiContext(agContext::ENV_PROD);
    $context->setUser(new agConfigUser($context));
    $controller = new ApiDocController($context);
    $context->setController($controller);
    $output = $controller->exec();
    echo $output;

} catch (Exception $ex) {
    echo $ex->getMessage();
}

?>
