<?php

namespace App\Repositories\Contracts;

use App\Models\Food;
use Illuminate\Database\Eloquent\Collection;

interface FoodRepositoryInterface
{
    /**
     * Get all foods in the catalog, ordered by name, optionally filtered by name.
     *
     * @return Collection<int, Food>
     */
    public function all(string $search = ''): Collection;

    /**
     * Search foods by name, ordered by name.
     *
     * @return Collection<int, Food>
     */
    public function search(string $term, int $limit = 8): Collection;

    /**
     * Find an existing food by name (case-insensitively) or create a new one.
     */
    public function firstOrCreate(string $name, ?int $calories = null): Food;

    /**
     * Create a food in the catalog.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Food;

    /**
     * Update a food in the catalog.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(Food $food, array $data): Food;

    /**
     * Delete a food from the catalog.
     */
    public function delete(Food $food): void;
}
