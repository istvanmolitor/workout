<?php

namespace Database\Factories;

use App\Models\Sleep;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sleep>
 */
class SleepFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startedAt = fake()->dateTimeBetween('-1 year', 'now');
        $endedAt = (clone $startedAt)->modify('+'.fake()->numberBetween(5 * 60, 9 * 60).' minutes');

        return [
            'user_id' => User::factory(),
            'started_at' => $startedAt,
            'ended_at' => $endedAt,
            'quality' => fake()->numberBetween(1, 5),
            'notes' => null,
        ];
    }
}
