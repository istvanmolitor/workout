<?php

namespace App\Repositories;

use App\Repositories\Contracts\NutritionLookupServiceInterface;
use Illuminate\Support\Facades\Http;

/**
 * Looks up nutrition data from Open Food Facts (https://world.openfoodfacts.org), a free,
 * key-less, community-maintained food database with Hungarian product data.
 */
class OpenFoodFactsNutritionLookupService implements NutritionLookupServiceInterface
{
    private const SEARCH_URL = 'https://world.openfoodfacts.org/cgi/search.pl';

    private const PRODUCT_URL = 'https://world.openfoodfacts.org/api/v2/product/%s.json';

    private const FIELDS = 'product_name,code,nutriments';

    public function lookupByName(string $name): ?array
    {
        $response = Http::timeout(5)->get(self::SEARCH_URL, [
            'search_terms' => $name,
            'search_simple' => 1,
            'action' => 'process',
            'json' => 1,
            'page_size' => 1,
            'lc' => 'hu',
            'fields' => self::FIELDS,
        ]);

        if ($response->failed()) {
            return null;
        }

        $nutriments = $response->json('products.0.nutriments');

        return $nutriments === null ? null : $this->mapNutriments($nutriments);
    }

    public function lookupByBarcode(string $barcode): ?array
    {
        $response = Http::timeout(5)->get(sprintf(self::PRODUCT_URL, $barcode), [
            'fields' => self::FIELDS,
        ]);

        if ($response->failed() || (int) $response->json('status') !== 1) {
            return null;
        }

        $nutriments = $response->json('product.nutriments');

        return $nutriments === null ? null : $this->mapNutriments($nutriments);
    }

    /**
     * Map Open Food Facts' per-100g nutriment keys (grams, except energy) onto our nutrient
     * catalog slugs. The 'calories' key is not a catalog nutrient; it maps to Food::$calories.
     *
     * @param  array<string, mixed>  $nutriments
     * @return array<string, int|float|null>
     */
    private function mapNutriments(array $nutriments): array
    {
        $grams = fn (string $key): ?float => isset($nutriments[$key]) ? round((float) $nutriments[$key], 2) : null;
        $milligrams = fn (string $key): ?float => isset($nutriments[$key]) ? round(((float) $nutriments[$key]) * 1_000, 2) : null;
        $micrograms = fn (string $key): ?float => isset($nutriments[$key]) ? round(((float) $nutriments[$key]) * 1_000_000, 2) : null;

        return [
            'calories' => isset($nutriments['energy-kcal_100g']) ? (int) round((float) $nutriments['energy-kcal_100g']) : null,
            'protein' => $grams('proteins_100g'),
            'fat' => $grams('fat_100g'),
            'saturated-fat' => $grams('saturated-fat_100g'),
            'carbohydrates' => $grams('carbohydrates_100g'),
            'sugar' => $grams('sugars_100g'),
            'fiber' => $grams('fiber_100g'),
            'salt' => $grams('salt_100g'),
            'sodium' => $milligrams('sodium_100g'),
            'cholesterol' => $milligrams('cholesterol_100g'),
            'calcium' => $milligrams('calcium_100g'),
            'iron' => $milligrams('iron_100g'),
            'potassium' => $milligrams('potassium_100g'),
            'magnesium' => $milligrams('magnesium_100g'),
            'vitamin-c' => $milligrams('vitamin-c_100g'),
            'vitamin-a' => $micrograms('vitamin-a_100g'),
            'vitamin-d' => $micrograms('vitamin-d_100g'),
        ];
    }
}
