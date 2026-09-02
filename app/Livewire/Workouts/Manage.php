<?php

namespace App\Livewire\Workouts;

use App\Models\Workout;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Edzések')]
class Manage extends Component
{
    /**
     * Get the authenticated user's logged workouts.
     *
     * @return Collection<int, Workout>
     */
    #[Computed]
    public function workouts(): Collection
    {
        return Auth::user()->workouts()->with('exercises.exercise', 'exercises.sets')->latest('performed_at')->latest()->get();
    }
}
