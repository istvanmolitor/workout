<?php

namespace App\Repositories\Contracts;

use App\Models\Meal;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

interface MealRepositoryInterface
{
    /**
     * Get the user's meal entries, most recently eaten first.
     *
     * @return Collection<int, Meal>
     */
    public function forUser(User $user): Collection;

    /**
     * Get the user's meal entries eaten on the given date, most recently eaten first.
     *
     * @return Collection<int, Meal>
     */
    public function forUserOnDate(User $user, Carbon $date): Collection;

    /**
     * Log a meal entry for the user with the given foods.
     *
     * @param  array<string, mixed>  $data
     * @param  array<int, int>  $foodIds
     */
    public function create(User $user, array $data, array $foodIds): Meal;

    /**
     * Update a meal entry and its foods.
     *
     * @param  array<string, mixed>  $data
     * @param  array<int, int>  $foodIds
     */
    public function update(Meal $meal, array $data, array $foodIds): Meal;

    /**
     * Delete a meal entry.
     */
    public function delete(Meal $meal): void;
}
