<?php

namespace App\Telegram\Commands;

use App\Models\Group;
use App\Telegram\Messages\KhotmilMessagesTrait;
use Telegram\Bot\Commands\Command;

class InfoTelegramCommand extends Command
{
    use KhotmilMessagesTrait;

    /**
     * @var string Command Name
     */
    protected $name = "info";

    /**
     * @var string Command Description
     */
    protected $description = "Show khotmil information";

    public function handle()
    {
        /** @var Group */
        $group = Group::where('telegram_chat_id', $this->update->getMessage()->chat->id)->with(['members' => function ($members)
        {
            $members->orderBy('order');
        }])->first();

        if (!$group) {
            $this->replyWithMessage([
                'parse_mode' => 'Markdown',
                "text" => $this->notRegistered()
            ]);
            return 0;
        }

        $this->replyWithMessage([
            'parse_mode' => 'Markdown',
            'text' => $this->info($group)
        ]);
    }
}
