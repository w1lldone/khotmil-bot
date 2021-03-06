<?php

namespace App\Telegram\Commands;

use App\Models\Group;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Objects\ChatMember;
use Telegram\Bot\Laravel\Facades\Telegram;
use App\Telegram\Messages\KhotmilMessagesTrait;

class StartTelegramCommand extends Command
{
    use KhotmilMessagesTrait;

    /**
     * @var string Command Name
     */
    protected $name = "start";

    /**
     * @var string Command Description
     */
    protected $description = "Start khotmil quran";

    public function handle()
    {
        /** @var Group */
        $group = Group::where('telegram_chat_id', $this->update->getMessage()->chat->id)->with('members')->first();

        if (!$group) {
            $this->replyWithMessage([
                'parse_mode' => 'Markdown',
                "text" => $this->notRegistered()
            ]);
            return 0;
        }

        $isAdmin = collect(Telegram::getChatAdministrators(['chat_id' => $group->telegram_chat_id]))->map(function (ChatMember $item) {
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

        if ($group->started_at) {
            $this->replyWithMessage([
                'parse_mode' => 'Markdown',
                "text" => $this->alreadyStarted()
            ]);
            return 0;
        }

        if ($group->members->count() == 0) {
            $this->replyWithMessage([
                'parse_mode' => 'Markdown',
                "text" => $this->hasNoMember()
            ]);
            return 0;
        }

        $group->increaseRound();
        $group->assignMemberSchedule(now());

        $this->replyWithMessage([
            'parse_mode' => 'Markdown',
            'text' => $this->progress($group)
        ]);

        return 1;
    }
}
