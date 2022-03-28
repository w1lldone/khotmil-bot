<?php

namespace App\Telegram\Commands;

use Telegram\Bot\Commands\Command;

class PingTelegramCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = "ping";

    /**
     * @var string Command Description
     */
    protected $description = "Ping this bot";

    public function handle()
    {
        $this->replyWithMessage(['text' => 'PONG!!']);
        return;
    }
}
