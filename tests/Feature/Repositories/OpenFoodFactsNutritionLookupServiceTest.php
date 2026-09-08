<?php

use App\Repositories\OpenFoodFactsNutritionLookupService;
use Illuminate\Support\Facades\Http;

test('lookup by name maps nutriments from the search endpoint', function () {
    Http::preventStrayRequests();
    Http::fake([
        'search.openfoodfacts.org/search*' => Http::response([
            'hits' => [
                [
                    'product_name' => 'Zabkása',
                    'nutriments' => [
                        'energy-kcal_100g' => 250,
                        'proteins_100g' => 12.5,
                        'fat_100g' => 9.2,
                        'saturated-fat_100g' => 3.1,
                        'carbohydrates_100g' => 55.4,
                        'sugars_100g' => 10.2,
                        'fiber_100g' => 4.5,
                        'salt_100g' => 1.25,
                        'sodium_100g' => 0.5,
                        'cholesterol_100g' => 0.03,
                        'calcium_100g' => 0.12,
                        'iron_100g' => 0.004,
                        'potassium_100g' => 0.3,
                        'magnesium_100g' => 0.05,
                        'vitamin-c_100g' => 0.006,
                        'vitamin-a_100g' => 0.00012,
                        'vitamin-d_100g' => 0.000005,
                    ],
                ],
            ],
        ]),
    ]);

    $result = (new OpenFoodFactsNutritionLookupService)->lookupByName('Zabkása');

    expect($result)->toBe([
        'calories' => 250,
        'protein' => 12.5,
        'fat' => 9.2,
        'saturated-fat' => 3.1,
        'carbohydrates' => 55.4,
        'sugar' => 10.2,
        'fiber' => 4.5,
        'salt' => 1.25,
        'sodium' => 500.0,
        'cholesterol' => 30.0,
        'calcium' => 120.0,
        'iron' => 4.0,
        'potassium' => 300.0,
        'magnesium' => 50.0,
        'vitamin-c' => 6.0,
        'vitamin-a' => 120.0,
        'vitamin-d' => 5.0,
    ]);
});

test('lookup by name returns null when no product is found', function () {
    Http::preventStrayRequests();
    Http::fake([
        'search.openfoodfacts.org/search*' => Http::response(['hits' => []]),
    ]);

    expect((new OpenFoodFactsNutritionLookupService)->lookupByName('Nonexistent food'))->toBeNull();
});

test('lookup by barcode maps nutriments from the product endpoint', function () {
    Http::preventStrayRequests();
    Http::fake([
        'world.openfoodfacts.org/api/v2/product/*' => Http::response([
            'status' => 1,
            'product' => [
                'product_name' => 'Zabkása',
                'nutriments' => [
                    'energy-kcal_100g' => 250,
                    'proteins_100g' => 12.5,
                    'sodium_100g' => 0.5,
                    'vitamin-a_100g' => 0.00012,
                ],
            ],
        ]),
    ]);

    $result = (new OpenFoodFactsNutritionLookupService)->lookupByBarcode('5901234123457');

    expect($result)->toMatchArray([
        'calories' => 250,
        'protein' => 12.5,
        'sodium' => 500.0,
        'vitamin-a' => 120.0,
    ]);
});

test('lookup by barcode returns null when the product is not found', function () {
    Http::preventStrayRequests();
    Http::fake([
        'world.openfoodfacts.org/api/v2/product/*' => Http::response(['status' => 0]),
    ]);

    expect((new OpenFoodFactsNutritionLookupService)->lookupByBarcode('0000000000000'))->toBeNull();
});

test('lookup by barcode returns null when the request fails', function () {
    Http::preventStrayRequests();
    Http::fake([
        'world.openfoodfacts.org/api/v2/product/*' => Http::response(null, 500),
    ]);

    expect((new OpenFoodFactsNutritionLookupService)->lookupByBarcode('5901234123457'))->toBeNull();
});
