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
            'order' => 0,
        ];
    }
}
