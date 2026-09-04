<?php

namespace App\Repositories;

use App\Models\BodyWeight;
use App\Models\User;
use App\Repositories\Contracts\BodyWeightRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class BodyWeightRepository implements BodyWeightRepositoryInterface
{
    public function forUser(User $user): Collection
    {
        return $user->bodyWeights()->latest('measured_at')->get();
    }

    public function recentForUser(User $user, int $limit): Collection
    {
        return $user->bodyWeights()->latest('measured_at')->limit($limit)->get();
    }

    public function create(User $user, array $data): BodyWeight
    {
        return $user->bodyWeights()->create($data);
    }

    public function update(BodyWeight $bodyWeight, array $data): BodyWeight
    {
        $bodyWeight->update($data);

        return $bodyWeight;
    }

    public function delete(BodyWeight $bodyWeight): void
    {
        $bodyWeight->delete();
    }
}
