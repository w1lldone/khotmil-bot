<?php

namespace App\Telegram\Commands;

use App\Models\Group;
use App\Telegram\Messages\KhotmilMessagesTrait;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\ChatMember;

class ResetTelegramCommand extends Command
{
    use KhotmilMessagesTrait;

    /**
     * @var string Command Name
     */
    protected $name = "reset";

    /**
     * @var string Command Description
     */
    protected $description = "Reset khotmil progress and member";

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

        $isAdmin = collect(Telegram::getChatAdministrators(['chat_id' => $group->telegram_chat_id]))->map(function (ChatMember $item)
        {
            return $item->user->id;
        })->search($this->update->message->from->id);

        if ($isAdmin === false) {
            $this->replyWithMessage([
                'parse_mode' => 'Markdown',
                "text" => $this->onlyForAdmin(),
                'reply_to_message_id' => $this->update->message->messageId
            ]);
            return 0;
        }

        $group->update(['started_at' => null, 'deadline' => null, 'round' => 0]);
        // $group->members()->delete();
        $group->schedules()->delete();
        $group->generateSchedules();

        $this->triggerCommand('new');
        return 1;
    }
}
