<?php

namespace App\Repositories\Contracts;

use App\Models\Field;
use Illuminate\Database\Eloquent\Collection;

interface FieldRepositoryInterface
{
    /**
     * Get all fields in the catalog, ordered by name.
     *
     * @return Collection<int, Field>
     */
    public function all(): Collection;

    /**
     * Create a field in the catalog.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Field;

    /**
     * Update a field in the catalog.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(Field $field, array $data): Field;

    /**
     * Delete a field from the catalog.
     */
    public function delete(Field $field): void;
}
