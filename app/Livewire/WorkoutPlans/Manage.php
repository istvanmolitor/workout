<?php

namespace App\Livewire\WorkoutPlans;

use App\Models\WorkoutPlan;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Edzéstervek')]
class Manage extends Component
{
    /**
     * Get the authenticated user's workout plans.
     *
     * @return Collection<int, WorkoutPlan>
     */
    #[Computed]
    public function workoutPlans(): Collection
    {
        return Auth::user()->workoutPlans()->with('exercises.exercise', 'exercises.sets.values.field')->latest()->get();
    }

    /**
     * Start a new workout from the given workout plan, copying its exercises.
     */
    public function startWorkout(WorkoutPlan $workoutPlan): void
    {
        $this->authorize('view', $workoutPlan);

        $workout = Auth::user()->workouts()->create([
            'workout_plan_id' => $workoutPlan->id,
            'name' => $workoutPlan->name,
            'performed_at' => now()->toDateString(),
        ]);

        $workoutPlan->load('exercises.sets.values');

        foreach ($workoutPlan->exercises as $planExercise) {
            $workoutExercise = $workout->exercises()->create([
                'exercise_id' => $planExercise->exercise_id,
                'order' => $planExercise->order,
            ]);

            foreach ($planExercise->sets as $planSet) {
                $workoutSet = $workoutExercise->sets()->create(['order' => $planSet->order]);

                foreach ($planSet->values as $planValue) {
                    $workoutSet->values()->create([
                        'field_id' => $planValue->field_id,
                        'value' => $planValue->value,
                    ]);
                }
            }
        }

        $this->redirectRoute('workouts.perform', $workout, navigate: true);
    }

    /**
     * Delete the given workout plan.
     */
    public function deleteWorkoutPlan(WorkoutPlan $workoutPlan): void
    {
        $this->authorize('delete', $workoutPlan);

        $workoutPlan->delete();

        unset($this->workoutPlans);

        Flux::toast(variant: 'success', text: __('Workout plan deleted.'));
    }
}
