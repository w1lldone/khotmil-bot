<?php

namespace App\Telegram\Commands;

use App\Models\Group;
use Telegram\Bot\Commands\Command;

class StartTelegramCommand extends Command
{
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
        $group = Group::where('telegram_chat_id', $this->update->getMessage()->chat->id)->first();

        if (!$group) {
            $this->replyWithMessage([
                'parse_mode' => 'Markdown',
                "text" => "Khotmil belum dimulai. Ketik /new untuk memulai"
            ]);
            return 1;
        }

        if ($group->started_at) {
            $this->replyWithMessage([
                'parse_mode' => 'Markdown',
                "text" => "Khotmil sudah dimulai sebelumnya. Ketik /info untuk melihat progres."
            ]);
            return 1;
        }

        $group->increaseRound();
        $group->assignMemberSchedule(now());

        $message = "*Khotmil Quran {$group->name} Putaran {$group->round}*
Periode {$group->started_at->format('d F Y')} - {$group->deadline->format('d F Y')}

";

        foreach ($group->schedules()->orderBy('juz')->with('member')->get() as $key => $schedule) {
            $juz = "Juz *{$schedule->juz}* ";
            if ($schedule->started_at) {
                $juz .= "{$schedule->getProgressIcon()} {$schedule->member->name}";
            }

            $message .= $juz."
";
        }

        $message .= "
{$group->onProgressIcon()} = Progres membaca
{$group->finishedIcon()} = Selesai";

        $this->replyWithMessage([
            'parse_mode' => 'Markdown',
            'text' => $message
        ]);
        return 1;
    }
}
