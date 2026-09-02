<?php

namespace App\Livewire\WorkoutPlans;

use App\Models\Exercise;
use App\Models\WorkoutPlan;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Edzésterv szerkesztése')]
class Edit extends Component
{
    #[Locked]
    public WorkoutPlan $workoutPlan;

    public string $name = '';

    public string $description = '';

    /**
     * @var array<int, array{exercise_id: int|string, sets: array<int, array{reps: int|string, weight: int|string|null}>}>
     */
    public array $exercises = [];

    /**
     * Mount the component.
     */
    public function mount(WorkoutPlan $workoutPlan): void
    {
        $this->authorize('update', $workoutPlan);

        $this->workoutPlan = $workoutPlan;
        $this->name = $workoutPlan->name;
        $this->description = $workoutPlan->description ?? '';
        $this->exercises = $workoutPlan->exercises
            ->map(fn ($exercise) => [
                'exercise_id' => $exercise->exercise_id,
                'sets' => $exercise->sets->map(fn ($set) => ['reps' => $set->reps, 'weight' => $set->weight])->all(),
            ])
            ->all();
    }

    /**
     * Get the exercises available to choose from.
     *
     * @return Collection<int, Exercise>
     */
    #[Computed]
    public function availableExercises(): Collection
    {
        return Exercise::query()->orderBy('name')->get();
    }

    /**
     * Add an empty exercise row to the form.
     */
    public function addExercise(): void
    {
        $this->exercises[] = ['exercise_id' => '', 'sets' => [['reps' => 10, 'weight' => null], ['reps' => 10, 'weight' => null], ['reps' => 10, 'weight' => null]]];
    }

    /**
     * Remove an exercise row from the form.
     */
    public function removeExercise(int $index): void
    {
        unset($this->exercises[$index]);

        $this->exercises = array_values($this->exercises);
    }

    /**
     * Add an empty set row to an exercise.
     */
    public function addSet(int $exerciseIndex): void
    {
        $this->exercises[$exerciseIndex]['sets'][] = ['reps' => 10, 'weight' => null];
    }

    /**
     * Remove a set row from an exercise.
     */
    public function removeSet(int $exerciseIndex, int $setIndex): void
    {
        unset($this->exercises[$exerciseIndex]['sets'][$setIndex]);

        $this->exercises[$exerciseIndex]['sets'] = array_values($this->exercises[$exerciseIndex]['sets']);
    }

    /**
     * Update the workout plan.
     */
    public function save(): void
    {
        $this->authorize('update', $this->workoutPlan);

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'exercises' => ['required', 'array', 'min:1'],
            'exercises.*.exercise_id' => ['required', 'integer', 'exists:exercises,id'],
            'exercises.*.sets' => ['required', 'array', 'min:1'],
            'exercises.*.sets.*.reps' => ['required', 'integer', 'min:1', 'max:999'],
            'exercises.*.sets.*.weight' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
        ]);

        $this->workoutPlan->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
        ]);

        $this->workoutPlan->exercises()->delete();

        foreach ($validated['exercises'] as $order => $exercise) {
            $planExercise = $this->workoutPlan->exercises()->create([
                'exercise_id' => $exercise['exercise_id'],
                'order' => $order,
            ]);

            foreach ($exercise['sets'] as $setOrder => $set) {
                $planExercise->sets()->create([
                    'reps' => $set['reps'],
                    'weight' => $set['weight'],
                    'order' => $setOrder,
                ]);
            }
        }

        Flux::toast(variant: 'success', text: __('Workout plan updated.'));

        $this->redirectRoute('workout-plans.index', navigate: true);
    }
}
