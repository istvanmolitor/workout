<?php

namespace Database\Factories;

use App\Models\Food;
use App\Models\Meal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Meal>
 */
class MealFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'eaten_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'type' => fake()->randomElement(['breakfast', 'lunch', 'dinner', 'snack']),
            'notes' => null,
        ];
    }

    /**
     * Configure the factory to attach a food when none was given.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Meal $meal): void {
            if ($meal->foods()->count() === 0) {
                $meal->foods()->attach(Food::factory()->create());
            }
        });
    }
}
