<?php

namespace App\Livewire\WorkoutPlans;

use App\Models\Exercise;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Új edzésterv')]
class Create extends Component
{
    public string $name = '';

    public string $description = '';

    /**
     * @var array<int, array{exercise_id: int|string, sets: array<int, array{reps: int|string, weight: int|string|null}>}>
     */
    public array $exercises = [
        ['exercise_id' => '', 'sets' => [['reps' => 10, 'weight' => null], ['reps' => 10, 'weight' => null], ['reps' => 10, 'weight' => null]]],
    ];

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
     * Create the workout plan for the authenticated user.
     */
    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'exercises' => ['required', 'array', 'min:1'],
            'exercises.*.exercise_id' => ['required', 'integer', 'exists:exercises,id'],
            'exercises.*.sets' => ['required', 'array', 'min:1'],
            'exercises.*.sets.*.reps' => ['required', 'integer', 'min:1', 'max:999'],
            'exercises.*.sets.*.weight' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
        ]);

        $workoutPlan = Auth::user()->workoutPlans()->create([
            'name' => $validated['name'],
            'description' => $validated['description'],
        ]);

        foreach ($validated['exercises'] as $order => $exercise) {
            $planExercise = $workoutPlan->exercises()->create([
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

        Flux::toast(variant: 'success', text: __('Workout plan created.'));

        $this->redirectRoute('workout-plans.index', navigate: true);
    }
}
