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
     * @var array<int, array{exercise_id: int|string, sets: int|string, reps: int|string}>
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
                'sets' => $exercise->sets,
                'reps' => $exercise->reps,
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
        $this->exercises[] = ['exercise_id' => '', 'sets' => 3, 'reps' => 10];
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
            'exercises.*.sets' => ['required', 'integer', 'min:1', 'max:99'],
            'exercises.*.reps' => ['required', 'integer', 'min:1', 'max:999'],
        ]);

        $this->workoutPlan->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
        ]);

        $this->workoutPlan->exercises()->delete();

        foreach ($validated['exercises'] as $order => $exercise) {
            $this->workoutPlan->exercises()->create([
                'exercise_id' => $exercise['exercise_id'],
                'sets' => $exercise['sets'],
                'reps' => $exercise['reps'],
                'order' => $order,
            ]);
        }

        Flux::toast(variant: 'success', text: __('Workout plan updated.'));

        $this->redirectRoute('workout-plans.index', navigate: true);
    }
}
