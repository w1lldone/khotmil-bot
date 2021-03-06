<?php

namespace App\Telegram\Commands;

use App\Models\Group;
use App\Telegram\Messages\KhotmilMessagesTrait;
use Telegram\Bot\Commands\Command;

class JoinTelegramCommand extends Command
{
    use KhotmilMessagesTrait;

    /**
     * @var string Command Name
     */
    protected $name = "join";

    /**
     * @var string Command Description
     */
    protected $description = "Join khotmil Quran";

    public function handle()
    {
        /** @var Group */
        $group = Group::where('telegram_chat_id', $this->update->getMessage()->chat->id)->first();

        if (!$group) {
            $this->replyWithMessage([
                'parse_mode' => 'Markdown',
                "text" => $this->notRegistered()
            ]);
            return 0;
        }

        $member = $group->members()->firstOrCreate(['telegram_user_id' => $this->update->getMessage()->from->id], [
            'name' => $this->update->getMessage()->from->firstName,
            'order' => $group->getLastMemberOrder() + 1
        ]);

        $this->replyWithMessage([
            'parse_mode' => 'Markdown',
            "text" => "Hai {$member->name}, selamat bergabung. Semoga Allah meridhoi 😊",
        ]);

        $this->replyWithMessage([
            'parse_mode' => 'Markdown',
            'text' => $this->info($group)
        ]);
    }
}
