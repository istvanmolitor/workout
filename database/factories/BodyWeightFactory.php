<?php

namespace Database\Factories;

use App\Models\BodyWeight;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BodyWeight>
 */
class BodyWeightFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'weight' => fake()->randomFloat(2, 50, 120),
            'measured_at' => fake()->date(),
        ];
    }
}
