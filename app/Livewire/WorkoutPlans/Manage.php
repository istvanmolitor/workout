<?php

namespace App\Livewire\WorkoutPlans;

use App\Models\WorkoutPlan;
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
        return Auth::user()->workoutPlans()->with('exercises.exercise', 'exercises.sets')->latest()->get();
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

        foreach ($workoutPlan->exercises as $planExercise) {
            $workoutExercise = $workout->exercises()->create([
                'exercise_id' => $planExercise->exercise_id,
                'order' => $planExercise->order,
            ]);

            foreach ($planExercise->sets as $planSet) {
                $workoutExercise->sets()->create([
                    'reps' => $planSet->reps,
                    'weight' => $planSet->weight,
                    'order' => $planSet->order,
                ]);
            }
        }

        $this->redirectRoute('workouts.perform', $workout, navigate: true);
    }
}
