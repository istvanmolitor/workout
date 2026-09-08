<?php

use App\Livewire\Foods\Create;
use App\Models\Food;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $this->get(route('foods.create'))->assertRedirect(route('login'));
});

test('non-admins cannot view the create food page', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('foods.create'))->assertForbidden();
});

test('create food page is displayed', function () {
    $this->actingAs(User::factory()->admin()->create());

    $this->get(route('foods.create'))->assertOk();
});

test('admin can create a food', function () {
    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(Create::class)
        ->set('name', 'Zabkása')
        ->set('calories', '350')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('foods.index'));

    $food = Food::query()->where('name', 'Zabkása')->first();
    expect($food)->not->toBeNull();
    expect($food->calories)->toBe(350);
});

test('food can be created without calories', function () {
    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(Create::class)
        ->set('name', 'Zabkása')
        ->call('save')
        ->assertHasNoErrors();

    expect(Food::query()->where('name', 'Zabkása')->first()->calories)->toBeNull();
});

test('food name is required', function () {
    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(Create::class)
        ->set('name', '')
        ->call('save')
        ->assertHasErrors(['name' => 'required']);
});

test('food name must be unique', function () {
    $this->actingAs(User::factory()->admin()->create());

    Food::factory()->create(['name' => 'Zabkása']);

    Livewire::test(Create::class)
        ->set('name', 'Zabkása')
        ->call('save')
        ->assertHasErrors(['name' => 'unique']);
});

test('admin can create a food with nutrient values', function () {
    $protein = createNutrient('protein', 'Fehérje');
    $vitaminC = createNutrient('vitamin-c', 'C-vitamin', 'mg');

    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(Create::class)
        ->set('name', 'Zabkása')
        ->set('barcode', '5901234123457')
        ->set('nutrientValues.protein', '12.5')
        ->set('nutrientValues.vitamin-c', '6')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('foods.index'));

    $food = Food::query()->where('name', 'Zabkása')->first();
    expect($food->barcode)->toBe('5901234123457');
    expect((float) $food->nutrients->find($protein)->pivot->value)->toBe(12.5);
    expect((float) $food->nutrients->find($vitaminC)->pivot->value)->toBe(6.0);
    expect($food->nutrition_synced)->toBeFalse();
});

test('food barcode must be unique', function () {
    $this->actingAs(User::factory()->admin()->create());

    Food::factory()->create(['barcode' => '5901234123457']);

    Livewire::test(Create::class)
        ->set('name', 'Zabkása')
        ->set('barcode', '5901234123457')
        ->call('save')
        ->assertHasErrors(['barcode' => 'unique']);
});

test('fetching nutrition data by barcode fills the form and marks it as synced', function () {
    createNutrient('protein', 'Fehérje');
    createNutrient('vitamin-c', 'C-vitamin', 'mg');

    Http::preventStrayRequests();
    Http::fake([
        'world.openfoodfacts.org/api/v2/product/*' => Http::response([
            'status' => 1,
            'product' => [
                'nutriments' => [
                    'energy-kcal_100g' => 250,
                    'proteins_100g' => 12.5,
                    'vitamin-c_100g' => 0.006,
                ],
            ],
        ]),
    ]);

    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(Create::class)
        ->set('name', 'Zabkása')
        ->set('barcode', '5901234123457')
        ->call('fetchNutrition')
        ->assertSet('calories', '250')
        ->assertSet('nutrientValues.protein', '12.5')
        ->assertSet('nutrientValues.vitamin-c', '6')
        ->assertSet('nutritionSynced', true);
});

test('fetching nutrition data by name is used when no barcode is given', function () {
    createNutrient('protein', 'Fehérje');

    Http::preventStrayRequests();
    Http::fake([
        'world.openfoodfacts.org/cgi/search.pl*' => Http::response([
            'products' => [
                ['nutriments' => ['energy-kcal_100g' => 120, 'proteins_100g' => 3]],
            ],
        ]),
    ]);

    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(Create::class)
        ->set('name', 'Zabkása')
        ->call('fetchNutrition')
        ->assertSet('calories', '120')
        ->assertSet('nutritionSynced', true);
});

test('fetching nutrition data does not mark the food as synced when nothing is found', function () {
    Http::preventStrayRequests();
    Http::fake([
        'world.openfoodfacts.org/cgi/search.pl*' => Http::response(['products' => []]),
    ]);

    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(Create::class)
        ->set('name', 'Nonexistent food')
        ->call('fetchNutrition')
        ->assertSet('nutritionSynced', false);
});
