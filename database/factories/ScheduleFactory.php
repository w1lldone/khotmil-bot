<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'juz' => $this->faker->numberBetween(1,30),
            'group_id' => \App\Models\Group::factory(),
            'member_id' => \App\Models\Member::factory(),
            'started_at' => now(),
            'deadline' => now()->addDays(7),
            'finished_at' => now()->addDays(8)
        ];
    }
}
