<?php

namespace Database\Factories;

use App\Models\WorkoutExercise;
use App\Models\WorkoutExerciseSet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkoutExerciseSet>
 */
class WorkoutExerciseSetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'workout_exercise_id' => WorkoutExercise::factory(),
            'reps' => fake()->numberBetween(1, 20),
            'completed_reps' => null,
            'weight' => fake()->randomFloat(2, 2.5, 100),
            'completed_weight' => null,
            'order' => 0,
        ];
    }
}
