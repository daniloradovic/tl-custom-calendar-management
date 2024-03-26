<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'start_time' => $this->faker->dateTime->format('Y-m-d H:i:s'),
            'end_time' => $this->faker->dateTime->format('Y-m-d H:i:s'),
            'description' => $this->faker->paragraph,
            'location' => $this->faker->city(),
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
