<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'group_id' => \App\Models\Group::factory(),
            'telegram_user_id' => $this->faker->randomNumber(5),
            'name' => $this->faker->name,
            'order' => $this->faker->numberBetween(1,10)
        ];
    }
}
