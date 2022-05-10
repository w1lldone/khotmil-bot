<?php

namespace App\Telegram\Commands;

use App\Models\Group;
use App\Telegram\Messages\KhotmilMessagesTrait;
use Telegram\Bot\Commands\Command;

class NewTelegramCommand extends Command
{
    use KhotmilMessagesTrait;

    /**
     * @var string Command Name
     */
    protected $name = "new";

    /**
     * @var string Command Description
     */
    protected $description = "New khotmil quran";

    public function handle()
    {
        /** @var Group */
        $group = Group::firstOrCreate(['telegram_chat_id' => $this->update->getMessage()->chat->id], [
            'name' => $this->update->getChat()->title,
            'duration' => 7,
            'timezone' => Group::getDefaultTimezone()
        ]);

        $this->replyWithMessage([
            'text' => $this->info($group),
            'parse_mode' => 'Markdown'
        ]);

        return 1;
    }
}
