<?php

namespace Database\Factories;

use App\Models\Nutrient;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Nutrient>
 */
class NutrientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = ucfirst(fake()->unique()->word());

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'unit' => fake()->randomElement(['g', 'mg', 'µg']),
            'decimal_places' => 2,
            'sort_order' => 0,
        ];
    }
}
