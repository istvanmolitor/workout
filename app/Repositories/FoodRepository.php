<?php

namespace App\Repositories;

use App\Models\Food;
use App\Repositories\Contracts\FoodRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FoodRepository implements FoodRepositoryInterface
{
    public function all(string $search = ''): Collection
    {
        return Food::query()
            ->when($search !== '', fn ($query) => $query->where('name', 'like', '%'.$search.'%'))
            ->orderBy('name')
            ->get();
    }

    public function search(string $term, int $limit = 8): Collection
    {
        return Food::query()
            ->where('name', 'like', '%'.$term.'%')
            ->orderBy('name')
            ->limit($limit)
            ->get();
    }

    public function firstOrCreate(string $name, ?int $calories = null): Food
    {
        $name = trim($name);

        return Food::query()->whereRaw('LOWER(name) = ?', [Str::lower($name)])->first()
            ?? Food::query()->create(['name' => $name, 'calories' => $calories]);
    }

    public function create(array $data, array $nutrients = []): Food
    {
        return DB::transaction(function () use ($data, $nutrients): Food {
            $food = Food::query()->create($data);

            $food->nutrients()->sync($nutrients);

            return $food;
        });
    }

    public function update(Food $food, array $data, array $nutrients = []): Food
    {
        DB::transaction(function () use ($food, $data, $nutrients): void {
            $food->update($data);

            $food->nutrients()->sync($nutrients);
        });

        return $food;
    }

    public function delete(Food $food): void
    {
        $food->delete();
    }
}
