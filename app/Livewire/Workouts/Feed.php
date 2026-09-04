<?php

namespace App\Livewire\Workouts;

use App\Models\Workout;
use App\Repositories\Contracts\WorkoutRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Követettek edzései')]
class Feed extends Component
{
    use WithPagination;

    protected WorkoutRepositoryInterface $workoutRepository;

    public function boot(WorkoutRepositoryInterface $workoutRepository): void
    {
        $this->workoutRepository = $workoutRepository;
    }

    /**
     * Get the logged workouts of the users the authenticated user is following.
     *
     * @return LengthAwarePaginator<int, Workout>
     */
    #[Computed]
    public function workouts(): LengthAwarePaginator
    {
        return $this->workoutRepository->feedForFollowing(Auth::user());
    }
}
