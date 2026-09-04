<?php

namespace App\Livewire\Workouts;

use App\Models\Workout;
use App\Repositories\Contracts\WorkoutRepositoryInterface;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Edzések')]
class Manage extends Component
{
    protected WorkoutRepositoryInterface $workoutRepository;

    public function boot(WorkoutRepositoryInterface $workoutRepository): void
    {
        $this->workoutRepository = $workoutRepository;
    }

    /**
     * Get the authenticated user's logged workouts.
     *
     * @return Collection<int, Workout>
     */
    #[Computed]
    public function workouts(): Collection
    {
        return $this->workoutRepository->forUser(Auth::user());
    }

    /**
     * Delete the given workout.
     */
    public function deleteWorkout(Workout $workout): void
    {
        $this->authorize('delete', $workout);

        $this->workoutRepository->delete($workout);

        unset($this->workouts);

        Flux::toast(variant: 'success', text: __('Workout deleted.'));
    }
}
