<?php

namespace Database\Seeders;

use App\Models\Exercise;
use App\Models\ExerciseCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExerciseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        collect([
            'Fekvenyomás' => 'Mell',
            'Guggolás' => 'Láb',
            'Felhúzás' => 'Hát',
            'Csigás lehúzás' => 'Hát',
            'Húzódzkodás' => 'Hát',
            'Vállból nyomás' => 'Váll',
            'Lábtolás' => 'Láb',
            'Bicepsz hajlítás' => 'Bicepsz',
            'Tricepsz nyomás' => 'Tricepsz',
            'Hasizom prés' => 'Has',
        ])->each(function (?string $categoryName, string $name) {
            $category = $categoryName ? ExerciseCategory::query()->where('name', $categoryName)->first() : null;

            Exercise::query()->updateOrCreate(['name' => $name], ['category_id' => $category?->id]);
        });
    }
}
