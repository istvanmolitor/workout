<?php

namespace Database\Factories;

use App\Models\ExerciseType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExerciseType>
 */
class ExerciseTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'single_set' => false,
        ];
    }
}
