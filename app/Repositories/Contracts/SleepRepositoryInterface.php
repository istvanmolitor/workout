<?php

namespace App\Repositories\Contracts;

use App\Models\Sleep;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

interface SleepRepositoryInterface
{
    /**
     * Get the user's sleep entries, most recently started first.
     *
     * @return Collection<int, Sleep>
     */
    public function forUser(User $user): Collection;

    /**
     * Get the user's sleep entries that started or ended on the given date, most recently started first.
     *
     * @return Collection<int, Sleep>
     */
    public function forUserOnDate(User $user, Carbon $date): Collection;

    /**
     * Log a sleep entry for the user.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(User $user, array $data): Sleep;

    /**
     * Update a sleep entry.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(Sleep $sleep, array $data): Sleep;

    /**
     * Delete a sleep entry.
     */
    public function delete(Sleep $sleep): void;
}
