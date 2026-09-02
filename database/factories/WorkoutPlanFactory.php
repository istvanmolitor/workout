<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\WorkoutPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkoutPlan>
 */
class WorkoutPlanFactory extends Factory
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
            'name' => fake()->words(3, true),
            'description' => fake()->optional()->sentence(),
        ];
    }
}
