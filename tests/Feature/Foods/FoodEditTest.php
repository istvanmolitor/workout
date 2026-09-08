<?php

use App\Livewire\Foods\Edit;
use App\Models\Food;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $food = Food::factory()->create();

    $this->get(route('foods.edit', $food))->assertRedirect(route('login'));
});

test('non-admins cannot view the edit food page', function () {
    $this->actingAs(User::factory()->create());
    $food = Food::factory()->create();

    $this->get(route('foods.edit', $food))->assertForbidden();
});

test('edit food page is displayed', function () {
    $this->actingAs(User::factory()->admin()->create());
    $food = Food::factory()->create();

    $this->get(route('foods.edit', $food))->assertOk();
});

test('admin can update a food', function () {
    $this->actingAs(User::factory()->admin()->create());
    $food = Food::factory()->create(['name' => 'Zabkása', 'calories' => 300]);

    Livewire::test(Edit::class, ['food' => $food])
        ->set('name', 'Zabpehely')
        ->set('calories', '320')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('foods.index'));

    $food->refresh();
    expect($food->name)->toBe('Zabpehely');
    expect($food->calories)->toBe(320);
});

test('food name is required', function () {
    $this->actingAs(User::factory()->admin()->create());
    $food = Food::factory()->create();

    Livewire::test(Edit::class, ['food' => $food])
        ->set('name', '')
        ->call('save')
        ->assertHasErrors(['name' => 'required']);
});

test('food name must be unique', function () {
    $this->actingAs(User::factory()->admin()->create());

    Food::factory()->create(['name' => 'Zabkása']);
    $food = Food::factory()->create(['name' => 'Rizs']);

    Livewire::test(Edit::class, ['food' => $food])
        ->set('name', 'Zabkása')
        ->call('save')
        ->assertHasErrors(['name' => 'unique']);
});

test('food can keep its own name unchanged', function () {
    $this->actingAs(User::factory()->admin()->create());
    $food = Food::factory()->create(['name' => 'Zabkása']);

    Livewire::test(Edit::class, ['food' => $food])
        ->set('name', 'Zabkása')
        ->call('save')
        ->assertHasNoErrors();
});
