<?php

namespace Database\Factories;

use App\Models\Group;
use Illuminate\Database\Eloquent\Factories\Factory;

class GroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'telegram_chat_id' => $this->faker->randomNumber(5),
            'name' => $this->faker->name,
            'round' => $this->faker->numberBetween(1,6),
            'duration' => $this->faker->numberBetween(5,14),
            'started_at' => now(),
            'deadline' => now()->addDays(7),
            'timezone' => Group::getDefaultTimezone(),
        ];
    }
}
