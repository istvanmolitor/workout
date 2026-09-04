<?php

namespace App\Repositories\Contracts;

use App\Models\ExerciseCategory;
use Illuminate\Database\Eloquent\Collection;

interface ExerciseCategoryRepositoryInterface
{
    /**
     * Get all exercise categories, ordered by name.
     *
     * @return Collection<int, ExerciseCategory>
     */
    public function all(): Collection;

    /**
     * Create an exercise category.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): ExerciseCategory;

    /**
     * Update an exercise category.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(ExerciseCategory $exerciseCategory, array $data): ExerciseCategory;

    /**
     * Delete an exercise category.
     */
    public function delete(ExerciseCategory $exerciseCategory): void;
}
