<?php

use App\Livewire\Nutrients\Edit;
use App\Models\Nutrient;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $nutrient = Nutrient::factory()->create();

    $this->get(route('nutrients.edit', $nutrient))->assertRedirect(route('login'));
});

test('non-admins cannot view the edit nutrient page', function () {
    $this->actingAs(User::factory()->create());
    $nutrient = Nutrient::factory()->create();

    $this->get(route('nutrients.edit', $nutrient))->assertForbidden();
});

test('edit nutrient page is displayed', function () {
    $this->actingAs(User::factory()->admin()->create());
    $nutrient = Nutrient::factory()->create();

    $this->get(route('nutrients.edit', $nutrient))->assertOk();
});

test('admin can update a nutrient', function () {
    $this->actingAs(User::factory()->admin()->create());
    $nutrient = Nutrient::factory()->create(['name' => 'Fehérje', 'slug' => 'protein', 'unit' => 'g']);

    Livewire::test(Edit::class, ['nutrient' => $nutrient])
        ->set('name', 'Fehérjetartalom')
        ->set('unit', 'mg')
        ->set('decimal_places', '3')
        ->set('sort_order', '5')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('nutrients.index'));

    $nutrient->refresh();
    expect($nutrient->name)->toBe('Fehérjetartalom');
    expect($nutrient->unit)->toBe('mg');
    expect($nutrient->decimal_places)->toBe(3);
    expect($nutrient->sort_order)->toBe(5);
});

test('nutrient name must be unique', function () {
    $this->actingAs(User::factory()->admin()->create());

    Nutrient::factory()->create(['name' => 'Fehérje']);
    $nutrient = Nutrient::factory()->create(['name' => 'Zsír']);

    Livewire::test(Edit::class, ['nutrient' => $nutrient])
        ->set('name', 'Fehérje')
        ->call('save')
        ->assertHasErrors(['name' => 'unique']);
});

test('nutrient can keep its own slug unchanged', function () {
    $this->actingAs(User::factory()->admin()->create());
    $nutrient = Nutrient::factory()->create(['slug' => 'protein']);

    Livewire::test(Edit::class, ['nutrient' => $nutrient])
        ->set('slug', 'protein')
        ->call('save')
        ->assertHasNoErrors();
});
