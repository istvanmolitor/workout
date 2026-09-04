<?php

namespace App\Repositories;

use App\Models\ExerciseCategory;
use App\Repositories\Contracts\ExerciseCategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ExerciseCategoryRepository implements ExerciseCategoryRepositoryInterface
{
    public function all(): Collection
    {
        return ExerciseCategory::query()->orderBy('name')->get();
    }

    public function create(array $data): ExerciseCategory
    {
        return ExerciseCategory::query()->create($data);
    }

    public function update(ExerciseCategory $exerciseCategory, array $data): ExerciseCategory
    {
        $exerciseCategory->update($data);

        return $exerciseCategory;
    }

    public function delete(ExerciseCategory $exerciseCategory): void
    {
        $exerciseCategory->delete();
    }
}
