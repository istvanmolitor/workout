<?php

namespace App\Repositories;

use App\Models\Sleep;
use App\Models\User;
use App\Repositories\Contracts\SleepRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class SleepRepository implements SleepRepositoryInterface
{
    public function forUser(User $user): Collection
    {
        return $user->sleeps()->latest('started_at')->get();
    }

    public function create(User $user, array $data): Sleep
    {
        return $user->sleeps()->create($data);
    }

    public function update(Sleep $sleep, array $data): Sleep
    {
        $sleep->update($data);

        return $sleep;
    }

    public function delete(Sleep $sleep): void
    {
        $sleep->delete();
    }
}
