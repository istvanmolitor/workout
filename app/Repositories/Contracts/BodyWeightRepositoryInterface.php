<?php

namespace App\Repositories\Contracts;

use App\Models\BodyWeight;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

interface BodyWeightRepositoryInterface
{
    /**
     * Get the user's body weight entries, most recently measured first.
     *
     * @return Collection<int, BodyWeight>
     */
    public function forUser(User $user): Collection;

    /**
     * Get the user's body weight entry measured on the given date, if any.
     */
    public function forUserOnDate(User $user, Carbon $date): ?BodyWeight;

    /**
     * Get the user's most recent body weight entries, most recently measured first.
     *
     * @return Collection<int, BodyWeight>
     */
    public function recentForUser(User $user, int $limit): Collection;

    /**
     * Log a body weight entry for the user.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(User $user, array $data): BodyWeight;

    /**
     * Update a body weight entry.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(BodyWeight $bodyWeight, array $data): BodyWeight;

    /**
     * Delete a body weight entry.
     */
    public function delete(BodyWeight $bodyWeight): void;
}
