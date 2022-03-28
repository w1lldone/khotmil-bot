<?php

namespace App\Telegram\Commands;

use App\Models\Group;
use Telegram\Bot\Commands\Command;

class NewTelegramCommand extends Command
{
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
            'duration' => 7
        ]);

        $members = $group->members()->get();

        $formattedMembers = "";

        foreach ($members as $key => $member) {
            $no = $key+1;
            $formattedMembers .= "{$no}. $member->name
";
        }

        dump($formattedMembers);

        $this->replyWithMessage([
        'text' => "
*Khotmil Qur'an {$group->name}*
Durasi: {$group->duration} hari

👥 *ANGGOTA*
$formattedMembers
Ketik /join untuk mengikuti khotmil ini
        ",
        'parse_mode' => 'Markdown']);

        return 1;
    }
}
