<?php

namespace Database\Seeders;

use App\Models\Exercise;
use App\Models\Field;
use App\Models\User;
use App\Models\Workout;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class WorkoutSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $templates = collect([
            'Felsőtest erő' => [
                'Fekvenyomás' => [['Ismétlés' => 10, 'Súly' => 60], ['Ismétlés' => 8, 'Súly' => 65], ['Ismétlés' => 8, 'Súly' => 65]],
                'Felhúzás' => [['Ismétlés' => 10, 'Súly' => 40], ['Ismétlés' => 10, 'Súly' => 40]],
                'Vállból nyomás' => [['Ismétlés' => 10, 'Súly' => 30], ['Ismétlés' => 8, 'Súly' => 32.5]],
            ],
            'Alsótest & kardió' => [
                'Guggolás' => [['Ismétlés' => 10, 'Súly' => 70], ['Ismétlés' => 8, 'Súly' => 80]],
                'Lábtolás' => [['Ismétlés' => 12, 'Súly' => 120]],
                'Futópad' => [['Idő' => 20, 'Táv' => 4]],
            ],
            'Testsúlyos kör' => [
                'Fekvőtámasz' => [['Ismétlés' => 15], ['Ismétlés' => 12]],
                'Testsúlyos guggolás' => [['Ismétlés' => 20], ['Ismétlés' => 18]],
                'Plank' => [['Idő' => 1]],
            ],
            'Futóedzés' => [
                'Terepfutás' => [['Táv' => 5, 'Idő' => 28]],
            ],
            'Reggeli jóga' => [
                'Napüdvözlet' => [['Idő' => 10]],
                'Harcos póz' => [['Idő' => 5]],
            ],
        ]);

        $users = User::query()->whereIn('email', array_values(UserSeeder::TEST_USER_NAMES))->get();

        $users->each(function (User $user) use ($templates) {
            $templates->keys()->shuffle()->take(fake()->numberBetween(2, 4))
                ->values()
                ->each(function (string $workoutName, int $index) use ($user, $templates) {
                    $workout = Workout::query()->firstOrCreate([
                        'user_id' => $user->id,
                        'name' => $workoutName,
                        'performed_at' => Carbon::now()->subDays($index * 3 + fake()->numberBetween(0, 2))->toDateString(),
                    ]);

                    if (! $workout->wasRecentlyCreated) {
                        return;
                    }

                    $order = 0;

                    collect($templates[$workoutName])->each(function (array $sets, string $exerciseName) use ($workout, &$order) {
                        $exercise = Exercise::query()->where('name', $exerciseName)->firstOrFail();

                        $workoutExercise = $workout->exercises()->create([
                            'exercise_id' => $exercise->id,
                            'order' => $order++,
                            'difficulty' => fake()->numberBetween(2, 5),
                        ]);

                        collect($sets)->values()->each(function (array $values, int $setOrder) use ($workoutExercise) {
                            $set = $workoutExercise->sets()->create(['order' => $setOrder]);

                            collect($values)->each(function (int|float $value, string $fieldName) use ($set) {
                                $field = Field::query()->where('name', $fieldName)->firstOrFail();

                                $set->values()->create([
                                    'field_id' => $field->id,
                                    'value' => $value,
                                    'completed_value' => $value,
                                ]);
                            });
                        });
                    });
                });
        });
    }
}
