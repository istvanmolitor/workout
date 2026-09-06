<?php

namespace App\Policies;

use App\Models\Sleep;
use App\Models\User;

class SleepPolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Sleep $sleep): bool
    {
        return $user->id === $sleep->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Sleep $sleep): bool
    {
        return $user->id === $sleep->user_id;
    }
}
