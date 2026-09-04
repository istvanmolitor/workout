<?php

namespace App\Livewire\WorkoutPlans;

use App\Models\WorkoutPlan;
use App\Repositories\Contracts\WorkoutPlanRepositoryInterface;
use App\Repositories\Contracts\WorkoutRepositoryInterface;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Edzéstervek')]
class Manage extends Component
{
    protected WorkoutPlanRepositoryInterface $workoutPlanRepository;

    protected WorkoutRepositoryInterface $workoutRepository;

    public function boot(WorkoutPlanRepositoryInterface $workoutPlanRepository, WorkoutRepositoryInterface $workoutRepository): void
    {
        $this->workoutPlanRepository = $workoutPlanRepository;
        $this->workoutRepository = $workoutRepository;
    }

    /**
     * Get the authenticated user's workout plans.
     *
     * @return Collection<int, WorkoutPlan>
     */
    #[Computed]
    public function workoutPlans(): Collection
    {
        return $this->workoutPlanRepository->forUser(Auth::user());
    }

    /**
     * Start a new workout from the given workout plan, copying its exercises.
     */
    public function startWorkout(WorkoutPlan $workoutPlan): void
    {
        $this->authorize('view', $workoutPlan);

        $workout = $this->workoutRepository->createFromPlan(Auth::user(), $workoutPlan);

        $this->redirectRoute('workouts.perform', $workout, navigate: true);
    }

    /**
     * Delete the given workout plan.
     */
    public function deleteWorkoutPlan(WorkoutPlan $workoutPlan): void
    {
        $this->authorize('delete', $workoutPlan);

        $this->workoutPlanRepository->delete($workoutPlan);

        unset($this->workoutPlans);

        Flux::toast(variant: 'success', text: __('Workout plan deleted.'));
    }
}
