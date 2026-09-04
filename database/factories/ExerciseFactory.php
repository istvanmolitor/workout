<?php

namespace Database\Factories;

use App\Models\Exercise;
use App\Models\ExerciseCategory;
use App\Models\ExerciseType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Exercise>
 */
class ExerciseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'category_id' => ExerciseCategory::factory(),
            'exercise_type_id' => ExerciseType::factory(),
        ];
    }
}
