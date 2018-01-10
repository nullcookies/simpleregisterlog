<?php
    /**
     * README
     * This file is intended to set the webhook.
     * Uncommented parameters must be filled
     */

    // Load composer
    require_once __DIR__ . '/vendor/autoload.php';

    // Add you bot's API key and name
    $bot_api_key  = '391634131:AAGRR0FvqFA0hj9dRHqDRbahrKowG441WKg';
    $bot_username = 'TestingVol4okBot';

    // Define the URL to your hook.php file
    //$hook_url = 'https://tlgrm.ias.su/lklf903r';
    $hook_url = 'https://simpleregisterlog.weborama.io/telegrambot/';

    try {
        // Create Telegram API object
        $telegram = new Longman\TelegramBot\Telegram($bot_api_key, $bot_username);

        // Set webhook
        $result = $telegram->setWebhook($hook_url);

        // To use a self-signed certificate, use this line instead
        //$result = $telegram->setWebhook($hook_url, ['certificate' => $certificate_path]);

        if ($result->isOk()) {
            echo $result->getDescription();
        }
    } catch (Longman\TelegramBot\Exception\TelegramException $e) {
        echo $e->getMessage();
    }
