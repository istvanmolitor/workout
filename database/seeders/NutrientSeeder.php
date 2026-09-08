<?php

namespace Database\Seeders;

use App\Models\Nutrient;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NutrientSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        collect([
            // Macronutrients, grams per 100g.
            ['name' => 'Fehérje', 'slug' => 'protein', 'unit' => 'g', 'sort_order' => 10],
            ['name' => 'Zsír', 'slug' => 'fat', 'unit' => 'g', 'sort_order' => 20],
            ['name' => 'Telített zsír', 'slug' => 'saturated-fat', 'unit' => 'g', 'sort_order' => 30],
            ['name' => 'Szénhidrát', 'slug' => 'carbohydrates', 'unit' => 'g', 'sort_order' => 40],
            ['name' => 'Cukor', 'slug' => 'sugar', 'unit' => 'g', 'sort_order' => 50],
            ['name' => 'Rost', 'slug' => 'fiber', 'unit' => 'g', 'sort_order' => 60],
            ['name' => 'Só', 'slug' => 'salt', 'unit' => 'g', 'sort_order' => 70],

            // Micronutrients, milligrams per 100g (vitamin A and D in micrograms).
            ['name' => 'Nátrium', 'slug' => 'sodium', 'unit' => 'mg', 'sort_order' => 80],
            ['name' => 'Koleszterin', 'slug' => 'cholesterol', 'unit' => 'mg', 'sort_order' => 90],
            ['name' => 'Kalcium', 'slug' => 'calcium', 'unit' => 'mg', 'sort_order' => 100],
            ['name' => 'Vas', 'slug' => 'iron', 'unit' => 'mg', 'sort_order' => 110],
            ['name' => 'Kálium', 'slug' => 'potassium', 'unit' => 'mg', 'sort_order' => 120],
            ['name' => 'Magnézium', 'slug' => 'magnesium', 'unit' => 'mg', 'sort_order' => 130],
            ['name' => 'C-vitamin', 'slug' => 'vitamin-c', 'unit' => 'mg', 'sort_order' => 140],
            ['name' => 'A-vitamin', 'slug' => 'vitamin-a', 'unit' => 'µg', 'sort_order' => 150],
            ['name' => 'D-vitamin', 'slug' => 'vitamin-d', 'unit' => 'µg', 'sort_order' => 160],
        ])->each(fn (array $nutrient) => Nutrient::query()->updateOrCreate(
            ['slug' => $nutrient['slug']],
            ['name' => $nutrient['name'], 'unit' => $nutrient['unit'], 'decimal_places' => 2, 'sort_order' => $nutrient['sort_order']]
        ));
    }
}
