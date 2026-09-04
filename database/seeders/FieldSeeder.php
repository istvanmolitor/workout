<?php

namespace Database\Seeders;

use App\Models\Field;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FieldSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        collect([
            'Ismétlés' => null,
            'Súly' => 'kg',
            'Táv' => 'km',
            'Idő' => 'perc',
        ])->each(fn (?string $unit, string $name) => Field::query()->updateOrCreate(['name' => $name], ['unit' => $unit]));
    }
}
