<?php

namespace App\Repositories\Contracts;

interface NutritionLookupServiceInterface
{
    /**
     * Look up nutrition values (per 100g) for a food by name using a free food database.
     * Keys are 'calories' plus the Nutrient catalog's slugs (e.g. 'protein', 'vitamin-c').
     *
     * @return array<string, int|float|null>|null
     */
    public function lookupByName(string $name): ?array;

    /**
     * Look up nutrition values (per 100g) for a food by barcode using a free food database.
     * Keys are 'calories' plus the Nutrient catalog's slugs (e.g. 'protein', 'vitamin-c').
     *
     * @return array<string, int|float|null>|null
     */
    public function lookupByBarcode(string $barcode): ?array;
}
