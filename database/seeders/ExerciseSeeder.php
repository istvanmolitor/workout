<?php

namespace Database\Seeders;

use App\Models\Exercise;
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
            'Fekvenyomás',
            'Guggolás',
            'Felhúzás',
            'Csigás lehúzás',
            'Húzódzkodás',
            'Vállból nyomás',
            'Lábtolás',
            'Bicepsz hajlítás',
            'Tricepsz nyomás',
            'Hasizom prés',
        ])->each(fn (string $name) => Exercise::query()->firstOrCreate(['name' => $name]));
    }
}
