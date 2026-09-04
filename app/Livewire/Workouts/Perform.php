<?php

namespace App\Livewire\Workouts;

use App\Models\Workout;
use App\Models\WorkoutExercise;
use App\Models\WorkoutExerciseSet;
use App\Models\WorkoutExerciseSetValue;
use App\Repositories\Contracts\WorkoutRepositoryInterface;
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
    protected WorkoutRepositoryInterface $workoutRepository;

    #[Locked]
    public Workout $workout;

    #[Locked]
    public ?int $activeExerciseId = null;

    /**
     * @var array<int, array{sets: array<int, array{values: array<int, array{value: string|null, completed_value: int|string|null}>}>, difficulty: int|string|null, note: string|null}>
     */
    public array $exercises = [];

    /**
     * Ids of the exercises whose results have been saved during this session.
     *
     * @var array<int, bool>
     */
    public array $logged = [];

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
                                            'completed_value' => $value->completed_value ?? $value->value,
                                        ],
                                    ])
                                    ->all(),
                            ],
                        ])
                        ->all(),
                    'difficulty' => $exercise->difficulty,
                    'note' => $exercise->note,
                ],
            ])
            ->all();

        $this->logged = $this->workout->exercises
            ->mapWithKeys(fn (WorkoutExercise $exercise) => [
                $exercise->id => $exercise->sets->isNotEmpty() && $exercise->sets->every(
                    fn (WorkoutExerciseSet $set) => $set->values->every(fn (WorkoutExerciseSetValue $value) => $value->completed_value !== null)
                ),
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

        $this->validate([
            "exercises.{$workoutExerciseId}.sets.*.values.*.completed_value" => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            "exercises.{$workoutExerciseId}.difficulty" => ['nullable', 'integer', 'min:1', 'max:5'],
            "exercises.{$workoutExerciseId}.note" => ['nullable', 'string', 'max:1000'],
        ]);

        foreach ($this->exercises[$workoutExerciseId]['sets'] as $setId => $set) {
            foreach ($set['values'] as $fieldId => $value) {
                $this->workoutRepository->updateSetValueCompletion($setId, $fieldId, $value['completed_value']);
            }
        }

        $this->workoutRepository->updateExercise($this->workout, $workoutExerciseId, [
            'difficulty' => $this->exercises[$workoutExerciseId]['difficulty'],
            'note' => $this->exercises[$workoutExerciseId]['note'],
        ]);

        $this->logged[$workoutExerciseId] = true;
        $this->activeExerciseId = null;

        Flux::toast(variant: 'success', text: __('Exercise logged.'));
    }
}
