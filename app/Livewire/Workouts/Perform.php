<?php

namespace App\Livewire\Workouts;

use App\Models\Workout;
use App\Models\WorkoutExercise;
use Flux\Flux;
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
     * @var array<int, array{completed_sets: int|string|null, completed_reps: int|string|null, difficulty: int|string|null}>
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
        $this->workout->load('exercises.exercise');

        $this->exercises = $this->workout->exercises
            ->mapWithKeys(fn (WorkoutExercise $exercise) => [
                $exercise->id => [
                    'completed_sets' => $exercise->completed_sets ?? $exercise->sets,
                    'completed_reps' => $exercise->completed_reps ?? $exercise->reps,
                    'difficulty' => $exercise->difficulty,
                ],
            ])
            ->all();

        $this->logged = $this->workout->exercises
            ->mapWithKeys(fn (WorkoutExercise $exercise) => [
                $exercise->id => $exercise->completed_sets !== null,
            ])
            ->all();
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
            "exercises.{$workoutExerciseId}.completed_sets" => ['nullable', 'integer', 'min:0', 'max:99'],
            "exercises.{$workoutExerciseId}.completed_reps" => ['nullable', 'integer', 'min:0', 'max:999'],
            "exercises.{$workoutExerciseId}.difficulty" => ['nullable', 'integer', 'min:1', 'max:10'],
        ]);

        $this->workout->exercises()->whereKey($workoutExerciseId)->update($validated['exercises'][$workoutExerciseId]);

        $this->logged[$workoutExerciseId] = true;
        $this->activeExerciseId = null;

        Flux::toast(variant: 'success', text: __('Exercise logged.'));
    }
}
