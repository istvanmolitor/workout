<?php

use App\Livewire\Foods\Create;
use App\Models\Food;
use App\Models\User;
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
