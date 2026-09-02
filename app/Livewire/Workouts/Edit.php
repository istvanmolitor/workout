<?php

namespace App\Livewire\Workouts;

use App\Models\Workout;
use App\Models\WorkoutExercise;
use App\Models\WorkoutExerciseSet;
use Flux\Flux;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Edzés naplózása')]
class Edit extends Component
{
    #[Locked]
    public Workout $workout;

    /**
     * @var array<int, array{sets: array<int, array{reps: int, completed_reps: int|string|null, weight: string|null, completed_weight: int|string|null}>, difficulty: int|string|null}>
     */
    public array $exercises = [];

    /**
     * Mount the component.
     */
    public function mount(Workout $workout): void
    {
        $this->authorize('update', $workout);

        $this->workout = $workout;
        $this->workout->load('exercises.exercise', 'exercises.sets');

        $this->exercises = $this->workout->exercises
            ->mapWithKeys(fn (WorkoutExercise $exercise) => [
                $exercise->id => [
                    'sets' => $exercise->sets
                        ->mapWithKeys(fn (WorkoutExerciseSet $set) => [
                            $set->id => [
                                'reps' => $set->reps,
                                'completed_reps' => $set->completed_reps,
                                'weight' => $set->weight,
                                'completed_weight' => $set->completed_weight,
                            ],
                        ])
                        ->all(),
                    'difficulty' => $exercise->difficulty,
                ],
            ])
            ->all();
    }

    /**
     * Save the logged results for the workout's exercises.
     */
    public function save(): void
    {
        $this->authorize('update', $this->workout);

        $validated = $this->validate([
            'exercises.*.sets.*.completed_reps' => ['nullable', 'integer', 'min:0', 'max:999'],
            'exercises.*.sets.*.completed_weight' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
            'exercises.*.difficulty' => ['nullable', 'integer', 'min:1', 'max:10'],
        ]);

        foreach ($validated['exercises'] as $workoutExerciseId => $data) {
            foreach ($data['sets'] as $setId => $set) {
                WorkoutExerciseSet::query()->whereKey($setId)->update([
                    'completed_reps' => $set['completed_reps'],
                    'completed_weight' => $set['completed_weight'],
                ]);
            }

            $this->workout->exercises()->whereKey($workoutExerciseId)->update([
                'difficulty' => $data['difficulty'],
            ]);
        }

        Flux::toast(variant: 'success', text: __('Workout logged.'));

        $this->redirectRoute('workouts.index', navigate: true);
    }
}
