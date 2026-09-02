<?php

namespace App\Livewire\WorkoutPlans;

use App\Models\Exercise;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('New workout plan')]
class Create extends Component
{
    public string $name = '';

    public string $description = '';

    /**
     * @var array<int, array{exercise_id: int|string, sets: int|string, reps: int|string}>
     */
    public array $exercises = [
        ['exercise_id' => '', 'sets' => 3, 'reps' => 10],
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
     * Create the workout plan for the authenticated user.
     */
    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'exercises' => ['required', 'array', 'min:1'],
            'exercises.*.exercise_id' => ['required', 'integer', 'exists:exercises,id'],
            'exercises.*.sets' => ['required', 'integer', 'min:1', 'max:99'],
            'exercises.*.reps' => ['required', 'integer', 'min:1', 'max:999'],
        ]);

        $workoutPlan = Auth::user()->workoutPlans()->create([
            'name' => $validated['name'],
            'description' => $validated['description'],
        ]);

        foreach ($validated['exercises'] as $order => $exercise) {
            $workoutPlan->exercises()->create([
                'exercise_id' => $exercise['exercise_id'],
                'sets' => $exercise['sets'],
                'reps' => $exercise['reps'],
                'order' => $order,
            ]);
        }

        Flux::toast(variant: 'success', text: __('Workout plan created.'));

        $this->redirectRoute('workout-plans.index', navigate: true);
    }
}
