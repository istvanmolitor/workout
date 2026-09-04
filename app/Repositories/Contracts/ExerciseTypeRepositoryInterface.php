<?php

namespace App\Repositories\Contracts;

use App\Models\ExerciseType;
use Illuminate\Database\Eloquent\Collection;

interface ExerciseTypeRepositoryInterface
{
    /**
     * Get all exercise types, ordered by name.
     *
     * @return Collection<int, ExerciseType>
     */
    public function all(): Collection;

    /**
     * Get all exercise types with their tracked fields, ordered by name.
     *
     * @return Collection<int, ExerciseType>
     */
    public function allWithFields(): Collection;

    /**
     * Create an exercise type together with the fields it tracks.
     *
     * @param  array<string, mixed>  $data
     * @param  array<int, array{field_id: int}>  $fields
     */
    public function create(array $data, array $fields): ExerciseType;

    /**
     * Update an exercise type and replace the fields it tracks.
     *
     * @param  array<string, mixed>  $data
     * @param  array<int, array{field_id: int}>  $fields
     */
    public function update(ExerciseType $exerciseType, array $data, array $fields): ExerciseType;

    /**
     * Delete an exercise type from the catalog.
     */
    public function delete(ExerciseType $exerciseType): void;
}
