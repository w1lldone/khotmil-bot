<?php

namespace App\Listeners;

use App\Events\GroupScheduleUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DispatchDeadlineReminder
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
        \App\Jobs\SendDeadlineReminder::dispatch($event->group)->delay(
            $event->group->deadline->setTimezone($event->group->timezone)->setTime(16,00)->setTimezone(config('app.timezone'))
        );
    }
}
