<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use App\Models\Workout;
use App\Models\WorkoutPlan;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

interface WorkoutRepositoryInterface
{
    /**
     * Get the user's logged workouts, most recently performed first.
     *
     * @return Collection<int, Workout>
     */
    public function forUser(User $user): Collection;

    /**
     * Get the user's logged workouts, paginated, most recently performed first.
     *
     * @return LengthAwarePaginator<int, Workout>
     */
    public function paginatedForUser(User $user, int $perPage = 10): LengthAwarePaginator;

    /**
     * Get the logged workouts of the users the given user is following, paginated.
     *
     * @return LengthAwarePaginator<int, Workout>
     */
    public function feedForFollowing(User $user, int $perPage = 10): LengthAwarePaginator;

    /**
     * Get the user's most recently logged workout.
     */
    public function lastForUser(User $user): ?Workout;

    /**
     * Get the user's workouts performed between the given dates.
     *
     * @return Collection<int, Workout>
     */
    public function betweenDatesForUser(User $user, Carbon $start, Carbon $end): Collection;

    /**
     * Create a workout for the user by copying the exercises, sets and values of a workout plan.
     */
    public function createFromPlan(User $user, WorkoutPlan $workoutPlan): Workout;

    /**
     * Update the completed value of a logged set's field.
     */
    public function updateSetValueCompletion(int $workoutExerciseSetId, int $fieldId, string|int|float|null $completedValue): void;

    /**
     * Update attributes on one of the workout's exercises.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateExercise(Workout $workout, int $workoutExerciseId, array $data): void;

    /**
     * Delete a workout.
     */
    public function delete(Workout $workout): void;
}
