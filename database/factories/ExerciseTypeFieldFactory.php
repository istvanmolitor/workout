<?php

namespace Database\Factories;

use App\Models\ExerciseType;
use App\Models\ExerciseTypeField;
use App\Models\Field;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExerciseTypeField>
 */
class ExerciseTypeFieldFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'exercise_type_id' => ExerciseType::factory(),
            'field_id' => Field::factory(),
            'order' => 0,
        ];
    }
}
