<?php

namespace Database\Factories;

use App\Models\Food;
use App\Models\Nutrient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Food>
 */
class FoodFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => ucfirst(fake()->unique()->word().' '.fake()->word()),
            'calories' => fake()->numberBetween(80, 900),
            'nutrition_synced' => false,
        ];
    }

    /**
     * Indicate that the food has a barcode and a full set of nutrient values, as if filled
     * in from the lookup service. Requires the nutrient catalog to already be seeded.
     */
    public function withNutrition(): static
    {
        return $this->state(fn (array $attributes): array => [
            'barcode' => fake()->unique()->ean13(),
            'nutrition_synced' => true,
        ])->afterCreating(function (Food $food): void {
            Nutrient::query()->get()->each(
                fn (Nutrient $nutrient) => $food->nutrients()->attach($nutrient->id, ['value' => fake()->randomFloat(2, 0, 100)])
            );
        });
    }
}
