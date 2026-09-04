<?php

namespace Database\Seeders;

use App\Models\ExerciseCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExerciseCategorySeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        collect([
            'Hát',
            'Bicepsz',
            'Tricepsz',
            'Has',
            'Váll',
            'Mell',
            'Láb',
            'Kardió',
            'Nyújtás',
            'Úszás',
            'Jóga',
            'Kerékpározás',
            'Evezés',
            'Testsúlyos edzés',
            'Kettlebell edzés',
            'Ugrókötelezés',
            'Boksz',
            'Spinning',
            'Pilates',
            'Túrázás',
            'HIIT',
            'Görkorcsolyázás',
        ])->each(fn (string $name) => ExerciseCategory::query()->firstOrCreate(['name' => $name]));
    }
}
