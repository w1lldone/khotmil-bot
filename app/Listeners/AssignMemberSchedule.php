<?php

namespace App\Listeners;

use App\Events\GroupScheduleUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AssignMemberSchedule
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\GroupScheduleUpdated  $event
     * @return void
     */
    public function handle(GroupScheduleUpdated $event)
    {
        $members = $event->group->members()->orderBy('order')->get();
        $schedules = $event->group->schedules()->whereNull('started_at')->orderBy('juz')->take($members->count())->get();

        foreach ($schedules as $key => $schedule) {
            $schedule->update(['member_id' => $members[$key]->id, 'started_at' => $event->group->started_at, 'deadline' => $event->group->deadline]);
        }
    }
}
