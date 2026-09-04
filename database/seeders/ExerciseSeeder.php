<?php

namespace Database\Seeders;

use App\Models\Exercise;
use App\Models\ExerciseCategory;
use App\Models\ExerciseType;
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
            'Súlyzós edzés' => [
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
            ],
            'Kondigép' => [
                'Futópad' => 'Kardió',
                'Elliptikus tréner' => 'Kardió',
                'Lépcsőzőgép' => 'Kardió',
            ],
            'Futás' => [
                'Terepfutás' => 'Kardió',
                'Intervallumfutás' => 'Kardió',
                'Hosszútávfutás' => 'Kardió',
            ],
            'Úszás' => [
                'Gyorsúszás' => 'Úszás',
                'Mellúszás' => 'Úszás',
                'Hátúszás' => 'Úszás',
                'Pillangóúszás' => 'Úszás',
            ],
            'Jóga' => [
                'Napüdvözlet' => 'Jóga',
                'Kutyapóz' => 'Jóga',
                'Harcos póz' => 'Jóga',
                'Gyermek póz' => 'Jóga',
                'Fa póz' => 'Jóga',
            ],
            'Kerékpározás' => [
                'Országúti kerékpározás' => 'Kerékpározás',
                'Mountain bike túra' => 'Kerékpározás',
                'Szobakerékpározás' => 'Kerékpározás',
            ],
            'Evezés' => [
                'Evezőgépes edzés' => 'Evezés',
                'Kajakozás' => 'Evezés',
                'Kenuzás' => 'Evezés',
            ],
            'Testsúlyos edzés' => [
                'Fekvőtámasz' => 'Testsúlyos edzés',
                'Plank' => 'Testsúlyos edzés',
                'Testsúlyos guggolás' => 'Testsúlyos edzés',
                'Kitörés' => 'Testsúlyos edzés',
                'Hasprés testsúllyal' => 'Testsúlyos edzés',
            ],
            'Kettlebell edzés' => [
                'Kettlebell lendítés' => 'Kettlebell edzés',
                'Kettlebell guggolás' => 'Kettlebell edzés',
                'Kettlebell felhúzás' => 'Kettlebell edzés',
            ],
            'Ugrókötelezés' => [
                'Ugrókötelezés' => 'Ugrókötelezés',
                'Váltott lábú ugrókötelezés' => 'Ugrókötelezés',
                'Dupla perdülés' => 'Ugrókötelezés',
            ],
            'Boksz' => [
                'Árnyékbox' => 'Boksz',
                'Zsákolás' => 'Boksz',
                'Páros boksz edzés' => 'Boksz',
            ],
            'Spinning' => [
                'Spinning óra' => 'Spinning',
                'Intervallum spinning' => 'Spinning',
            ],
            'Pilates' => [
                'Pilates alapgyakorlatok' => 'Pilates',
                'Pilates core edzés' => 'Pilates',
            ],
            'Nyújtás' => [
                'Statikus nyújtás' => 'Nyújtás',
                'Dinamikus nyújtás' => 'Nyújtás',
                'Hamstring nyújtás' => 'Nyújtás',
            ],
            'Túrázás' => [
                'Hegyi túra' => 'Túrázás',
                'Erdei túra' => 'Túrázás',
                'Dombtúra' => 'Túrázás',
            ],
            'HIIT' => [
                'Tabata edzés' => 'HIIT',
                'Burpee intervallum' => 'HIIT',
                'Körkörös HIIT edzés' => 'HIIT',
            ],
            'Görkorcsolyázás' => [
                'Görkorcsolyázás' => 'Görkorcsolyázás',
                'Gördeszkázás' => 'Görkorcsolyázás',
            ],
        ])->each(function (array $exercises, string $typeName) {
            $exerciseType = ExerciseType::query()->where('name', $typeName)->firstOrFail();

            collect($exercises)->each(function (?string $categoryName, string $name) use ($exerciseType) {
                $category = $categoryName ? ExerciseCategory::query()->where('name', $categoryName)->first() : null;

                Exercise::query()->updateOrCreate(['name' => $name], [
                    'category_id' => $category?->id,
                    'exercise_type_id' => $exerciseType->id,
                ]);
            });
        });
    }
}
