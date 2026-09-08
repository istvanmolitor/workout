<?php

use App\Livewire\Nutrients\Create;
use App\Models\Nutrient;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $this->get(route('nutrients.create'))->assertRedirect(route('login'));
});

test('non-admins cannot view the create nutrient page', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('nutrients.create'))->assertForbidden();
});

test('create nutrient page is displayed', function () {
    $this->actingAs(User::factory()->admin()->create());

    $this->get(route('nutrients.create'))->assertOk();
});

test('admin can create a nutrient', function () {
    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(Create::class)
        ->set('name', 'Fehérje')
        ->set('slug', 'protein')
        ->set('unit', 'g')
        ->set('decimal_places', '2')
        ->set('sort_order', '10')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('nutrients.index'));

    $nutrient = Nutrient::query()->where('slug', 'protein')->first();
    expect($nutrient)->not->toBeNull();
    expect($nutrient->name)->toBe('Fehérje');
    expect($nutrient->unit)->toBe('g');
    expect($nutrient->decimal_places)->toBe(2);
    expect($nutrient->sort_order)->toBe(10);
});

test('nutrient name is required', function () {
    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(Create::class)
        ->set('name', '')
        ->set('slug', 'protein')
        ->set('unit', 'g')
        ->call('save')
        ->assertHasErrors(['name' => 'required']);
});

test('nutrient name must be unique', function () {
    $this->actingAs(User::factory()->admin()->create());

    Nutrient::factory()->create(['name' => 'Fehérje']);

    Livewire::test(Create::class)
        ->set('name', 'Fehérje')
        ->set('slug', 'protein-2')
        ->set('unit', 'g')
        ->call('save')
        ->assertHasErrors(['name' => 'unique']);
});

test('nutrient slug must be unique', function () {
    $this->actingAs(User::factory()->admin()->create());

    Nutrient::factory()->create(['slug' => 'protein']);

    Livewire::test(Create::class)
        ->set('name', 'Fehérje 2')
        ->set('slug', 'protein')
        ->set('unit', 'g')
        ->call('save')
        ->assertHasErrors(['slug' => 'unique']);
});

test('nutrient slug must be lowercase kebab-case', function () {
    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(Create::class)
        ->set('name', 'Fehérje')
        ->set('slug', 'Protein Value!')
        ->set('unit', 'g')
        ->call('save')
        ->assertHasErrors(['slug' => 'regex']);
});
