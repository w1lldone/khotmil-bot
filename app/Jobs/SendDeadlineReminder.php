<?php

namespace App\Jobs;

use App\Models\Group;
use App\Telegram\Messages\KhotmilMessagesTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Telegram\Bot\Laravel\Facades\Telegram;

class SendDeadlineReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, KhotmilMessagesTrait;

    public $group;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Group $group)
    {
        $this->group = $group;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $schedules = $this->group->schedules()->whereNull('finished_at')->where('deadline', '<=', now()->toDateTimeString())->with('member')->get();

        if (!$schedules->count()) {
            return;
        }

        $message = "Assalamualaikum. Khotmil Quran sudah deadline, nih!

";
        $message .= $this->remainingSchedules($schedules);

        $message .= "
Semoga Allah meridhoi. Aaamiin 🤲";

        try {
            Telegram::sendMessage([
                'chat_id' => $this->group->telegram_chat_id,
                'parse_mode' => "Markdown",
                'text' => $message
            ]);
        } catch (\Throwable $th) {
        }

        SendDeadlineReminder::dispatch($this->group, now()->addDay()->toDateTimeString());
    }
}
