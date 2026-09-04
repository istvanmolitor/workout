<?php

namespace App\Livewire\Workouts;

use App\Models\Workout;
use App\Models\WorkoutExercise;
use App\Models\WorkoutExerciseSet;
use App\Models\WorkoutExerciseSetValue;
use App\Repositories\Contracts\WorkoutRepositoryInterface;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Edzés naplózása')]
class Edit extends Component
{
    protected WorkoutRepositoryInterface $workoutRepository;

    #[Locked]
    public Workout $workout;

    /**
     * @var array<int, array{sets: array<int, array{values: array<int, array{value: string|null, completed_value: int|string|null}>}>, difficulty: int|string|null}>
     */
    public array $exercises = [];

    public function boot(WorkoutRepositoryInterface $workoutRepository): void
    {
        $this->workoutRepository = $workoutRepository;
    }

    /**
     * Mount the component.
     */
    public function mount(Workout $workout): void
    {
        $this->authorize('update', $workout);

        $this->workout = $workout;
        $this->workout->load('exercises.exercise', 'exercises.sets.values.field');

        $this->exercises = $this->workout->exercises
            ->mapWithKeys(fn (WorkoutExercise $exercise) => [
                $exercise->id => [
                    'sets' => $exercise->sets
                        ->mapWithKeys(fn (WorkoutExerciseSet $set) => [
                            $set->id => [
                                'values' => $set->values
                                    ->mapWithKeys(fn (WorkoutExerciseSetValue $value) => [
                                        $value->field_id => [
                                            'value' => $value->value,
                                            'completed_value' => $value->completed_value,
                                        ],
                                    ])
                                    ->all(),
                            ],
                        ])
                        ->all(),
                    'difficulty' => $exercise->difficulty,
                ],
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
     * Save the logged results for the workout's exercises.
     */
    public function save(): void
    {
        $this->authorize('update', $this->workout);

        $this->validate([
            'exercises.*.sets.*.values.*.completed_value' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'exercises.*.difficulty' => ['nullable', 'integer', 'min:1', 'max:5'],
        ]);

        foreach ($this->exercises as $workoutExerciseId => $data) {
            foreach ($data['sets'] as $setId => $set) {
                foreach ($set['values'] as $fieldId => $value) {
                    $this->workoutRepository->updateSetValueCompletion($setId, $fieldId, $value['completed_value']);
                }
            }

            $this->workoutRepository->updateExercise($this->workout, $workoutExerciseId, [
                'difficulty' => $data['difficulty'],
            ]);
        }

        Flux::toast(variant: 'success', text: __('Workout logged.'));

        $this->redirectRoute('workouts.index', navigate: true);
    }
}
