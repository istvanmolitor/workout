<?php

namespace Database\Factories;

use App\Models\WorkoutPlan;
use App\Models\WorkoutPlanExercise;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkoutPlanExercise>
 */
class WorkoutPlanExerciseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'workout_plan_id' => WorkoutPlan::factory(),
            'name' => fake()->words(2, true),
            'sets' => fake()->numberBetween(1, 5),
            'reps' => fake()->numberBetween(1, 20),
            'order' => 0,
        ];
    }
}
