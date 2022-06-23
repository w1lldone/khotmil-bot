<?php

namespace Tests\Feature\Models;

use App\Models\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ScheduleModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created_using_factory()
    {
        $schedule = Schedule::factory()->create();

        $this->assertDatabaseHas('schedules', [
            'id' => $schedule->id
        ]);
    }
}
