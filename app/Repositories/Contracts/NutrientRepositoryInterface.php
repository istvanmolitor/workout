<?php

namespace App\Repositories\Contracts;

use App\Models\Nutrient;
use Illuminate\Database\Eloquent\Collection;

interface NutrientRepositoryInterface
{
    /**
     * Get all nutrients in the catalog, ordered for display.
     *
     * @return Collection<int, Nutrient>
     */
    public function all(): Collection;

    /**
     * Create a nutrient in the catalog.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Nutrient;

    /**
     * Update a nutrient in the catalog.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(Nutrient $nutrient, array $data): Nutrient;

    /**
     * Delete a nutrient from the catalog.
     */
    public function delete(Nutrient $nutrient): void;
}
