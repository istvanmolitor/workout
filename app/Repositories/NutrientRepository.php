<?php

namespace App\Repositories;

use App\Models\Nutrient;
use App\Repositories\Contracts\NutrientRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class NutrientRepository implements NutrientRepositoryInterface
{
    public function all(): Collection
    {
        return Nutrient::query()->orderBy('sort_order')->orderBy('name')->get();
    }

    public function create(array $data): Nutrient
    {
        return Nutrient::query()->create($data);
    }

    public function update(Nutrient $nutrient, array $data): Nutrient
    {
        $nutrient->update($data);

        return $nutrient;
    }

    public function delete(Nutrient $nutrient): void
    {
        $nutrient->delete();
    }
}
