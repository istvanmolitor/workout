<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use App\Models\WorkoutPlan;
use Illuminate\Database\Eloquent\Collection;

interface WorkoutPlanRepositoryInterface
{
    /**
     * Get the user's workout plans, most recently created first.
     *
     * @return Collection<int, WorkoutPlan>
     */
    public function forUser(User $user): Collection;

    /**
     * Create a workout plan with its exercises, sets and values for the user.
     *
     * @param  array<int, array{exercise_id: int|string, sets: array<int, array{values: array<int, int|string|null>}>}>  $exercises
     */
    public function create(User $user, string $name, ?string $description, array $exercises): WorkoutPlan;

    /**
     * Update a workout plan, replacing its exercises, sets and values.
     *
     * @param  array<int, array{exercise_id: int|string, sets: array<int, array{values: array<int, int|string|null>}>}>  $exercises
     */
    public function update(WorkoutPlan $workoutPlan, string $name, ?string $description, array $exercises): WorkoutPlan;

    /**
     * Delete a workout plan.
     */
    public function delete(WorkoutPlan $workoutPlan): void;
}
