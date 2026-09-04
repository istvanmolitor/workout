<?php

namespace Database\Factories;

use App\Models\Field;
use App\Models\WorkoutPlanExerciseSet;
use App\Models\WorkoutPlanExerciseSetValue;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkoutPlanExerciseSetValue>
 */
class WorkoutPlanExerciseSetValueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'workout_plan_exercise_set_id' => WorkoutPlanExerciseSet::factory(),
            'field_id' => Field::factory(),
            'value' => fake()->randomFloat(2, 1, 100),
        ];
    }
}
