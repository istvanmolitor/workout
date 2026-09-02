<?php

namespace App\Livewire\Workouts;

use App\Models\Workout;
use App\Models\WorkoutExercise;
use App\Models\WorkoutExerciseSet;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::workout')]
#[Title('Edzés')]
class Perform extends Component
{
    #[Locked]
    public Workout $workout;

    #[Locked]
    public ?int $activeExerciseId = null;

    /**
     * @var array<int, array{sets: array<int, array{reps: int, completed_reps: int|string|null, weight: string|null, completed_weight: int|string|null}>, difficulty: int|string|null}>
     */
    public array $exercises = [];

    /**
     * Ids of the exercises whose results have been saved during this session.
     *
     * @var array<int, bool>
     */
    public array $logged = [];

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
                                'completed_reps' => $set->completed_reps ?? $set->reps,
                                'weight' => $set->weight,
                                'completed_weight' => $set->completed_weight ?? $set->weight,
                            ],
                        ])
                        ->all(),
                    'difficulty' => $exercise->difficulty,
                ],
            ])
            ->all();

        $this->logged = $this->workout->exercises
            ->mapWithKeys(fn (WorkoutExercise $exercise) => [
                $exercise->id => $exercise->sets->isNotEmpty() && $exercise->sets->every(fn (WorkoutExerciseSet $set) => $set->completed_reps !== null),
            ])
            ->all();
    }

    /**
     * Get the translated labels for each difficulty level, keyed by level.
     *
     * @return array<int, string>
     */
    #[Computed]
    public function difficultyLabels(): array
    {
        return WorkoutExercise::difficultyLabels();
    }

    /**
     * Focus a single exercise to log its results.
     */
    public function selectExercise(int $workoutExerciseId): void
    {
        $this->activeExerciseId = $workoutExerciseId;
    }

    /**
     * Return to the exercise overview without saving.
     */
    public function back(): void
    {
        $this->activeExerciseId = null;
    }

    /**
     * Save the logged results for the active exercise.
     */
    public function save(): void
    {
        $this->authorize('update', $this->workout);

        $workoutExerciseId = $this->activeExerciseId;

        $validated = $this->validate([
            "exercises.{$workoutExerciseId}.sets.*.completed_reps" => ['nullable', 'integer', 'min:0', 'max:999'],
            "exercises.{$workoutExerciseId}.sets.*.completed_weight" => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
            "exercises.{$workoutExerciseId}.difficulty" => ['nullable', 'integer', 'min:1', 'max:5'],
        ]);

        foreach ($validated['exercises'][$workoutExerciseId]['sets'] as $setId => $set) {
            WorkoutExerciseSet::query()->whereKey($setId)->update([
                'completed_reps' => $set['completed_reps'],
                'completed_weight' => $set['completed_weight'],
            ]);
        }

        $this->workout->exercises()->whereKey($workoutExerciseId)->update([
            'difficulty' => $validated['exercises'][$workoutExerciseId]['difficulty'],
        ]);

        $this->logged[$workoutExerciseId] = true;
        $this->activeExerciseId = null;

        Flux::toast(variant: 'success', text: __('Exercise logged.'));
    }
}
