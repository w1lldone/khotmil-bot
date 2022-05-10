<?php

namespace App\Telegram\Commands;

use App\Models\Group;
use App\Telegram\Messages\KhotmilMessagesTrait;
use Telegram\Bot\Commands\Command;

class ProgressTelegramCommand extends Command
{
    use KhotmilMessagesTrait;

    /**
     * @var string Command Name
     */
    protected $name = "progress";

    /**
     * @var string Command Description
     */
    protected $description = "Show khotmil progress";

    public function handle()
    {
        /** @var Group */
        $group = Group::where('telegram_chat_id', $this->update->getMessage()->chat->id)->first();

        if (!$group) {
            $this->replyWithMessage([
                'parse_mode' => 'Markdown',
                "text" => $this->notRegistered()
            ]);
            return 1;
        }

        if (!$group->started_at) {
            $this->replyWithMessage([
                'parse_mode' => 'Markdown',
                "text" => $this->notStarted()
            ]);
            return 0;
        }

        $this->replyWithMessage([
            'parse_mode' => 'Markdown',
            'text' => $this->progress($group)
        ]);
    }
}
