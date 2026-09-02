<?php

namespace App\Livewire\WorkoutPlans;

use App\Models\WorkoutPlan;
use Flux\Flux;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Edit workout plan')]
class Edit extends Component
{
    #[Locked]
    public WorkoutPlan $workoutPlan;

    public string $name = '';

    public string $description = '';

    /**
     * @var array<int, array{name: string, sets: int|string, reps: int|string}>
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
                'name' => $exercise->name,
                'sets' => $exercise->sets,
                'reps' => $exercise->reps,
            ])
            ->all();
    }

    /**
     * Add an empty exercise row to the form.
     */
    public function addExercise(): void
    {
        $this->exercises[] = ['name' => '', 'sets' => 3, 'reps' => 10];
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
            'exercises.*.name' => ['required', 'string', 'max:255'],
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
                'name' => $exercise['name'],
                'sets' => $exercise['sets'],
                'reps' => $exercise['reps'],
                'order' => $order,
            ]);
        }

        Flux::toast(variant: 'success', text: __('Workout plan updated.'));

        $this->redirectRoute('workout-plans.index', navigate: true);
    }
}
