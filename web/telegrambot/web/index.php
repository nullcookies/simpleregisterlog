<?php
    ini_set('error_reporting', E_ALL);
    ini_set('display_errors', 1);

    //phpinfo(); exit;

    // Load composer
    require_once dirname(__FILE__).'/../vendor/autoload.php';

    // Add you bot's API key and name
    $bot_api_key  = '391634131:AAGRR0FvqFA0hj9dRHqDRbahrKowG441WKg';
    $bot_username = 'TestingVol4okBot';

    // Define all IDs of admin users in this array (leave as empty array if not used)
    $admin_users = [
        //    123,
    ];

    // Define all paths for your custom commands in this array (leave as empty array if not used)
    $commands_paths = [
        dirname(__FILE__).'/../Commands/',
    ];

    // Enter your MySQL database credentials
    $mysql_credentials = [
        'host'     => 'localhost',
        'user'     => 'telegrambot',
        'password' => 'telegrambot',
        'database' => 'telegrambot',
    ];

//    phpinfo(); exit;

    $dsn     = 'mysql:host=' . $mysql_credentials['host'] . ';dbname=' . $mysql_credentials['database'];
    $options = [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'];

    try {
        $pdo = new PDO($dsn, $mysql_credentials['user'], $mysql_credentials['password'], $options);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }

    //var_dump($dsn, $options); exit;
    exit;

    try {
        // Create Telegram API object
        $telegram = new Longman\TelegramBot\Telegram($bot_api_key, $bot_username);

        // Add commands paths containing your custom commands
        $telegram->addCommandsPaths($commands_paths);

        // Enable admin users
        //$telegram->enableAdmins($admin_users);

        // Enable MySQL
        $telegram->enableMySql($mysql_credentials, null, 'utf8');

        // Logging (Error, Debug and Raw Updates)
        Longman\TelegramBot\TelegramLog::initErrorLog(dirname(__FILE__)."/../log/{$bot_username}_error.log");
        Longman\TelegramBot\TelegramLog::initDebugLog(dirname(__FILE__)."/../log/{$bot_username}_debug.log");
        Longman\TelegramBot\TelegramLog::initUpdateLog(dirname(__FILE__)."/../log/{$bot_username}_update.log");

        // If you are using a custom Monolog instance for logging, use this instead of the above
        //Longman\TelegramBot\TelegramLog::initialize($your_external_monolog_instance);

        // Set custom Upload and Download paths
        //$telegram->setDownloadPath(__DIR__ . '/Download');
        //$telegram->setUploadPath(__DIR__ . '/Upload');

        // Here you can set some command specific parameters
        // e.g. Google geocode/timezone api key for /date command
        //$telegram->setCommandConfig('date', ['google_api_key' => 'your_google_api_key_here']);

        // Botan.io integration
        //$telegram->enableBotan('your_botan_token');

        // Requests Limiter (tries to prevent reaching Telegram API limits)
        $telegram->enableLimiter();

        // Handle telegram webhook request
        $telegram->handle();

    } catch (Longman\TelegramBot\Exception\TelegramException $e) {
        // Silence is golden!
        echo $e;
        // Log telegram errors
        Longman\TelegramBot\TelegramLog::error($e);
    } catch (Longman\TelegramBot\Exception\TelegramLogException $e) {
        // Silence is golden!
        // Uncomment this to catch log initialisation errors
        echo $e;
    }

file_put_contents(dirname(__FILE__).'/../log/webhook.log', date('Y-m-d H:i:s').' INPUT: '.file_get_contents('php://input').' e:'.(string) $e."\n", FILE_APPEND);
