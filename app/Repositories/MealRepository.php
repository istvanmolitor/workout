<?php

namespace App\Repositories;

use App\Models\Meal;
use App\Models\User;
use App\Repositories\Contracts\MealRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class MealRepository implements MealRepositoryInterface
{
    public function forUser(User $user): Collection
    {
        return $user->meals()->with('foods')->latest('eaten_at')->get();
    }

    public function forUserOnDate(User $user, Carbon $date): Collection
    {
        return $user->meals()
            ->with('foods')
            ->whereDate('eaten_at', $date)
            ->latest('eaten_at')
            ->get();
    }

    public function create(User $user, array $data, array $foods): Meal
    {
        $meal = $user->meals()->create($data);

        $meal->foods()->sync($foods);

        return $meal;
    }

    public function update(Meal $meal, array $data, array $foods): Meal
    {
        $meal->update($data);

        $meal->foods()->sync($foods);

        return $meal;
    }

    public function delete(Meal $meal): void
    {
        $meal->delete();
    }
}
