<?php

namespace Database\Seeders;

use App\Models\Exercise;
use App\Models\Field;
use App\Models\User;
use App\Models\WorkoutPlan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WorkoutPlanSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::query()->where('email', 'admin@example.com')->firstOrFail();

        collect([
            'Felsőtest erő' => [
                'description' => 'Mell, hát, váll és kar erősítése súlyzós gyakorlatokkal.',
                'exercises' => [
                    'Fekvenyomás' => [['Ismétlés' => 10, 'Súly' => 60], ['Ismétlés' => 8, 'Súly' => 65], ['Ismétlés' => 8, 'Súly' => 65]],
                    'Felhúzás' => [['Ismétlés' => 10, 'Súly' => 40], ['Ismétlés' => 10, 'Súly' => 40], ['Ismétlés' => 8, 'Súly' => 45]],
                    'Vállból nyomás' => [['Ismétlés' => 10, 'Súly' => 30], ['Ismétlés' => 10, 'Súly' => 30], ['Ismétlés' => 8, 'Súly' => 32.5]],
                    'Bicepsz hajlítás' => [['Ismétlés' => 12, 'Súly' => 15], ['Ismétlés' => 12, 'Súly' => 15]],
                    'Tricepsz nyomás' => [['Ismétlés' => 12, 'Súly' => 20], ['Ismétlés' => 12, 'Súly' => 20]],
                ],
            ],
            'Alsótest & kardió' => [
                'description' => 'Láberősítés súlyzóval, zárva egy futópados kardió blokkal.',
                'exercises' => [
                    'Guggolás' => [['Ismétlés' => 10, 'Súly' => 70], ['Ismétlés' => 8, 'Súly' => 80], ['Ismétlés' => 8, 'Súly' => 80]],
                    'Lábtolás' => [['Ismétlés' => 12, 'Súly' => 120], ['Ismétlés' => 10, 'Súly' => 140]],
                    'Futópad' => [['Idő' => 20, 'Táv' => 4]],
                ],
            ],
            'Testsúlyos edzés otthonra' => [
                'description' => 'Eszköz nélküli, otthon is elvégezhető kör.',
                'exercises' => [
                    'Fekvőtámasz' => [['Ismétlés' => 15], ['Ismétlés' => 15], ['Ismétlés' => 12]],
                    'Testsúlyos guggolás' => [['Ismétlés' => 20], ['Ismétlés' => 20]],
                    'Kitörés' => [['Ismétlés' => 12], ['Ismétlés' => 12]],
                    'Plank' => [['Idő' => 1], ['Idő' => 1]],
                ],
            ],
        ])->each(function (array $plan, string $planName) use ($user) {
            $workoutPlan = WorkoutPlan::query()->updateOrCreate(
                ['user_id' => $user->id, 'name' => $planName],
                ['description' => $plan['description']],
            );

            $order = 0;

            collect($plan['exercises'])->each(function (array $sets, string $exerciseName) use ($workoutPlan, &$order) {
                $exercise = Exercise::query()->where('name', $exerciseName)->firstOrFail();

                $workoutPlanExercise = $workoutPlan->exercises()->updateOrCreate(
                    ['exercise_id' => $exercise->id],
                    ['order' => $order++],
                );

                collect($sets)->values()->each(function (array $values, int $setOrder) use ($workoutPlanExercise) {
                    $set = $workoutPlanExercise->sets()->updateOrCreate(['order' => $setOrder]);

                    collect($values)->each(function (int|float $value, string $fieldName) use ($set) {
                        $field = Field::query()->where('name', $fieldName)->firstOrFail();

                        $set->values()->updateOrCreate(['field_id' => $field->id], ['value' => $value]);
                    });
                });
            });
        });
    }
}
