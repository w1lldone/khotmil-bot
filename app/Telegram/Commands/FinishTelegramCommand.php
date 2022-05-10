<?php

namespace App\Telegram\Commands;

use App\Models\Group;
use App\Models\Schedule;
use App\Telegram\Messages\KhotmilMessagesTrait;
use Telegram\Bot\Commands\Command;

class FinishTelegramCommand extends Command
{
    use KhotmilMessagesTrait;

    /**
     * @var string Command Name
     */
    protected $name = "finish";

    /**
     * @var string Command Description
     */
    protected $description = "Finish reading Quran";

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

        if (!$group->started_at) {
            $this->replyWithMessage([
                'parse_mode' => 'Markdown',
                "text" => $this->notStarted()
            ]);
            return 0;
        }

        $member = $group->members()->where('telegram_user_id', $this->update->message->from->id)->first();

        if (!$member) {
            $this->replyWithMessage([
                'parse_mode' => 'Markdown',
                'text' => $this->notAMember()
            ]);
            return 0;
        }

        /** @var Schedule */
        $schedule = $group->schedules()->whereNull('finished_at')->whereNotNull('started_at')->where('member_id', $member->id)->first();

        if (!$schedule) {
            $this->replyWithMessage([
                'parse_mode' => 'Markdown',
                'text' => $this->noActiveSchedule($member),
                'reply_to_message_id' => $this->update->message->messageId
            ]);
            return 0;
        }

        $schedule->update(['finished_at' => now()]);

        $remaining = $group->schedules()->whereNotNull('started_at')->whereNull('finished_at')->with('member')->get();

        $message = $this->readingFinished($member);

        if ($remaining->count() == 0) {

            $emptySchedules = $group->schedules()->whereNull('member_id')->count();

            if ($emptySchedules == null) {
                $group->increaseRound();
                $group->resetMemberOrder();
                $group->schedules()->delete();
                $group->generateSchedules();
            }

            $group->assignMemberSchedule(now()->addDay());
        } else {
            $message .= $this->remainingSchedules($remaining);
        }

        $this->replyWithMessage([
            'parse_mode' => 'Markdown',
            'text' => $message,
            'reply_to_message_id' => $this->update->message->messageId
        ]);

        $this->triggerCommand('progress');
    }
}
