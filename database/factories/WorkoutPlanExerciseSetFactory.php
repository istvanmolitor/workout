<?php

namespace Database\Factories;

use App\Models\WorkoutPlanExercise;
use App\Models\WorkoutPlanExerciseSet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkoutPlanExerciseSet>
 */
class WorkoutPlanExerciseSetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'workout_plan_exercise_id' => WorkoutPlanExercise::factory(),
            'reps' => fake()->numberBetween(1, 20),
            'weight' => fake()->randomFloat(2, 2.5, 100),
            'order' => 0,
        ];
    }
}
