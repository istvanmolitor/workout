<?php

namespace Database\Factories;

use App\Models\Field;
use App\Models\WorkoutExerciseSet;
use App\Models\WorkoutExerciseSetValue;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkoutExerciseSetValue>
 */
class WorkoutExerciseSetValueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'workout_exercise_set_id' => WorkoutExerciseSet::factory(),
            'field_id' => Field::factory(),
            'value' => fake()->randomFloat(2, 1, 100),
            'completed_value' => null,
        ];
    }
}
