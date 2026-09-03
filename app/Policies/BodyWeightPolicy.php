<?php

namespace App\Policies;

use App\Models\BodyWeight;
use App\Models\User;

class BodyWeightPolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, BodyWeight $bodyWeight): bool
    {
        return $user->id === $bodyWeight->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BodyWeight $bodyWeight): bool
    {
        return $user->id === $bodyWeight->user_id;
    }
}
