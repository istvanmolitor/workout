<?php

namespace App\Repositories\Contracts;

use App\Models\Exercise;
use Illuminate\Database\Eloquent\Collection;

interface ExerciseRepositoryInterface
{
    /**
     * Get all exercises in the catalog, grouped by category name.
     *
     * @return Collection<string, Collection<int, Exercise>>
     */
    public function allGroupedByCategory(): Collection;

    /**
     * Get all exercises with their exercise type and tracked fields, ordered by name.
     *
     * @return Collection<int, Exercise>
     */
    public function allWithExerciseType(): Collection;

    /**
     * Create an exercise in the catalog.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Exercise;

    /**
     * Update an exercise in the catalog.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(Exercise $exercise, array $data): Exercise;

    /**
     * Delete an exercise from the catalog.
     */
    public function delete(Exercise $exercise): void;
}
