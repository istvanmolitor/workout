<?php

use App\Livewire\Foods\Manage;
use App\Models\Food;
use App\Models\Meal;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $this->get(route('foods.index'))->assertRedirect(route('login'));
});

test('non-admins cannot view the foods page', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('foods.index'))->assertForbidden();
});

test('foods page is displayed', function () {
    $this->actingAs(User::factory()->admin()->create());

    $this->get(route('foods.index'))->assertOk();
});

test('lists foods in the catalog', function () {
    $this->actingAs(User::factory()->admin()->create());

    Food::factory()->create(['name' => 'Zabkása']);
    Food::factory()->create(['name' => 'Rizs']);

    Livewire::test(Manage::class)
        ->assertSee('Zabkása')
        ->assertSee('Rizs');
});

test('searching filters foods by name', function () {
    $this->actingAs(User::factory()->admin()->create());

    Food::factory()->create(['name' => 'Zabkása']);
    Food::factory()->create(['name' => 'Rizs']);

    Livewire::test(Manage::class)
        ->set('search', 'zab')
        ->assertSee('Zabkása')
        ->assertDontSee('Rizs');
});

test('an unused food can be deleted', function () {
    $this->actingAs(User::factory()->admin()->create());

    $food = Food::factory()->create();

    Livewire::test(Manage::class)
        ->call('delete', $food->id);

    expect(Food::query()->find($food->id))->toBeNull();
});

test('a food used in a meal cannot be deleted', function () {
    $this->actingAs(User::factory()->admin()->create());

    $food = Food::factory()->create();
    $meal = Meal::factory()->create();
    $meal->foods()->attach($food);

    Livewire::test(Manage::class)
        ->call('delete', $food->id);

    expect(Food::query()->find($food->id))->not->toBeNull();
});
