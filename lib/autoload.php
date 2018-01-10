<?php

    function __autoload($class) {
        $class = preg_replace('/[^\w\d]/imu', '', $class);
        $basedir = dirname(__FILE__);
        $basedirs = array(
            dirname($basedir)."/apps/".NOMVC_APPNAME."/" => array(
                'controllers',
                'models',
                'models/route',
                'models/control',
                'models/dashboard',
                'models/stat',
                'models/api',
                'tasks',
                'base'
            ),
            "{$basedir}/core/" => array(
                'controllers',
                'users',
                'models',
                'widgets',
                'validators',
                'helpers',
                'exceptions'
            ),
            "{$basedir}/external/" => array(
                'yaml',
                'xls',
                'mailer/PHPMailer',
                'mailer/PHPMailer6/src',
                'spore/PHPSpore/lib',
            ),
            "{$basedir}/tools/" => array(
                'CsvReader',
                'mail',
                ''
            )
        );
        
        foreach ($basedirs as $basedir => $dirs) {
            foreach ($dirs as $dir) {
                $files = array(
                    "{$basedir}/{$dir}/{$class}.class.php",
                    "{$basedir}/{$dir}/{$class}.php",
                );
                foreach ($files as $file) {
                    if (file_exists($file)) {
                        require_once($file);
                        return true;
                    }
                }
            }
        }
        if (phpExcelAutoLoad($class)) {
            return true;
        }
        eval("class $class {}");
        throw new nomvcClassNotFoundException($class);
    }

    function phpExcelAutoLoad($pClassName) {
        //define('PHPEXCEL_ROOT', dirname(__FILE__).'/external/xls/PHPExcel/');
        //require_once(dirname(__FILE__).'/external/xls/PHPExcel/Autoloader.php');

        if ((class_exists($pClassName, false)) || (strpos($pClassName, 'PHPExcel') !== 0)) {
            return false;
        }
        $pClassFilePath = PHPEXCEL_ROOT.str_replace('_',DIRECTORY_SEPARATOR,$pClassName).'.php';
        if ((file_exists($pClassFilePath) === false) || (is_readable($pClassFilePath) === false)) {
            return false;
        }
        require($pClassFilePath);
        return true;
    }
