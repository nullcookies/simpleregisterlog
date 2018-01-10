<?php
/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Request;
use PDO;

/**
 * HelloMessage command
 */
class HellomessageCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'hellomessage';

    /**
     * @var string
     */
    protected $description = 'Send hello message';

    /**
     * @var string
     */
    protected $version = '1.0.0';

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        $conn = $this->telegram->getDb();
        $stmt = $conn->prepare('select id from `chat`');
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($results as $key => $chat){
            $chat_id = $chat['id'];
            $text    = 'это исходящее сообщение';

            $data = [
                'chat_id' => $chat_id,
                'text'    => $text,
            ];

            Request::sendMessage($data);
        }

        return true;
    }
}