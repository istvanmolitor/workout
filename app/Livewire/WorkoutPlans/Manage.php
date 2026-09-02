<?php

namespace App\Livewire\WorkoutPlans;

use App\Models\WorkoutPlan;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Workout plans')]
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
        return Auth::user()->workoutPlans()->with('exercises')->latest()->get();
    }
}
