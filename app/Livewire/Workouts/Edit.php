<?php

namespace App\Livewire\Workouts;

use App\Models\Workout;
use App\Models\WorkoutExercise;
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
     * @var array<int, array{completed_sets: int|string|null, completed_reps: int|string|null, difficulty: int|string|null}>
     */
    public array $exercises = [];

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
                    'completed_sets' => $exercise->completed_sets,
                    'completed_reps' => $exercise->completed_reps,
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
            'exercises.*.completed_sets' => ['nullable', 'integer', 'min:0', 'max:99'],
            'exercises.*.completed_reps' => ['nullable', 'integer', 'min:0', 'max:999'],
            'exercises.*.difficulty' => ['nullable', 'integer', 'min:1', 'max:10'],
        ]);

        foreach ($validated['exercises'] as $workoutExerciseId => $data) {
            $this->workout->exercises()->whereKey($workoutExerciseId)->update($data);
        }

        Flux::toast(variant: 'success', text: __('Workout logged.'));

        $this->redirectRoute('workouts.index', navigate: true);
    }
}
