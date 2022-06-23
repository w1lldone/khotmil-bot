<?php

namespace Tests\Feature\Jobs;

use App\Jobs\SendDeadlineReminder;
use App\Models\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use Telegram\Bot\Laravel\Facades\Telegram;
use Tests\TestCase;

class SendDeadlineReminderTest extends TestCase
{
    /** @test */
    public function it_should_send_reminder_to_unfinished_schedules()
    {
        $schedule = Schedule::factory()->create([
            'finished_at' => null,
            'deadline' => now()->subDay()
        ]);
        $job = new SendDeadlineReminder($schedule->group);

        Telegram::shouldReceive('sendMessage')->once();
        $job->handle();
    }

    /** @test */
    public function it_should_pushed_another_reminder()
    {
        Queue::fake();
        $schedule = Schedule::factory()->create([
            'finished_at' => null,
            'deadline' => now()->subDay()
        ]);
        $job = new SendDeadlineReminder($schedule->group);

        Telegram::shouldReceive('sendMessage')->once();
        $job->handle();
        Queue::assertPushed(SendDeadlineReminder::class, 1);
    }

    /** @test */
    public function it_does_not_send_reminder_on_finished_schedules()
    {
        Queue::fake();
        $schedule = Schedule::factory()->create([
            'finished_at' => now(),
            'deadline' => now()->subDay()
        ]);
        $job = new SendDeadlineReminder($schedule->group);

        Telegram::shouldReceive('sendMessage')->never();
        $job->handle();
        Queue::assertNotPushed(SendDeadlineReminder::class);
    }
}
