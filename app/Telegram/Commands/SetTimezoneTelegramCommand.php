<?php

namespace App\Telegram\Commands;

use App\Models\Group;
use App\Telegram\Messages\KhotmilMessagesTrait;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Objects\ChatMember;
use Telegram\Bot\Laravel\Facades\Telegram;

class SetTimezoneTelegramCommand extends Command
{
    use KhotmilMessagesTrait;

    /**
     * @var string Command Name
     */
    protected $name = "settimezone";

    /** @var string The Telegram command description. */
    protected $description = "Set timezone";

    /** @var string Command Argument Pattern */
    protected $pattern = '{timezone}';

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

        $timezone = $this->getArguments()['timezone'];

        try {
            now()->setTimezone($timezone);
        } catch (\Throwable $th) {
            $this->replyWithMessage([
                'parse_mode' => 'Markdown',
                'text' => "Ups! {$timezone} bukan timezone yang valid. Coba lagi ya 😅"
            ]);
            return 0;
        }

        $group->update(['timezone' => $timezone]);
        $this->replyWithMessage([
            'parse_mode' => 'Markdown',
            'text' => $this->info($group)
        ]);
        return 1;
    }
}
