<?php

namespace App\Livewire\Workouts;

use App\Models\Workout;
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

    /**
     * Get the logged workouts of the users the authenticated user is following.
     *
     * @return LengthAwarePaginator<int, Workout>
     */
    #[Computed]
    public function workouts(): LengthAwarePaginator
    {
        return Workout::query()
            ->whereIn('user_id', Auth::user()->following()->pluck('users.id'))
            ->with('user', 'exercises.exercise', 'exercises.sets.values.field')
            ->latest('performed_at')
            ->latest()
            ->paginate(10);
    }
}
