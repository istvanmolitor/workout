<?php

namespace App\Repositories;

use App\Models\Sleep;
use App\Models\User;
use App\Repositories\Contracts\SleepRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class SleepRepository implements SleepRepositoryInterface
{
    public function forUser(User $user): Collection
    {
        return $user->sleeps()->latest('started_at')->get();
    }

    public function forUserOnDate(User $user, Carbon $date): Collection
    {
        return $user->sleeps()
            ->where(function ($query) use ($date): void {
                $query->whereDate('started_at', $date)->orWhereDate('ended_at', $date);
            })
            ->latest('started_at')
            ->get();
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
