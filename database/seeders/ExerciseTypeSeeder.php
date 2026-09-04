<?php

namespace Database\Seeders;

use App\Models\ExerciseType;
use App\Models\Field;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExerciseTypeSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        collect([
            'Súlyzós edzés' => ['Ismétlés', 'Súly'],
            'Kondigép' => ['Idő', 'Táv'],
            'Futás' => ['Táv', 'Idő'],
            'Úszás' => ['Táv', 'Idő'],
            'Jóga' => ['Idő'],
            'Kerékpározás' => ['Táv', 'Idő'],
            'Evezés' => ['Táv', 'Idő'],
            'Testsúlyos edzés' => ['Ismétlés'],
            'Kettlebell edzés' => ['Ismétlés', 'Súly'],
            'Ugrókötelezés' => ['Idő'],
            'Boksz' => ['Idő'],
            'Spinning' => ['Idő', 'Táv'],
            'Pilates' => ['Idő'],
            'Nyújtás' => ['Idő'],
            'Túrázás' => ['Táv', 'Idő'],
            'HIIT' => ['Idő'],
            'Görkorcsolyázás' => ['Táv', 'Idő'],
        ])->each(function (array $fieldNames, string $typeName) {
            $exerciseType = ExerciseType::query()->firstOrCreate(['name' => $typeName]);

            foreach ($fieldNames as $order => $fieldName) {
                $field = Field::query()->where('name', $fieldName)->firstOrFail();

                $exerciseType->fields()->updateOrCreate(['field_id' => $field->id], ['order' => $order]);
            }
        });
    }
}
