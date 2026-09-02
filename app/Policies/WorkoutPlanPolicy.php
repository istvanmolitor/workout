<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkoutPlan;

class WorkoutPlanPolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, WorkoutPlan $workoutPlan): bool
    {
        return $user->id === $workoutPlan->user_id;
    }
}
