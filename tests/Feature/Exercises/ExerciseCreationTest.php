<?php

use App\Livewire\Exercises\Create;
use App\Models\Exercise;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $this->get(route('exercises.create'))->assertRedirect(route('login'));
});

test('create exercise page is displayed', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('exercises.create'))->assertOk();
});

test('authenticated user can create an exercise', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(Create::class)
        ->set('name', 'Bench press')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('exercises.index'));

    expect(Exercise::query()->where('name', 'Bench press')->exists())->toBeTrue();
});

test('exercise name is required', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(Create::class)
        ->set('name', '')
        ->call('save')
        ->assertHasErrors(['name' => 'required']);
});

test('exercise name must be unique', function () {
    $this->actingAs(User::factory()->create());

    Exercise::factory()->create(['name' => 'Bench press']);

    Livewire::test(Create::class)
        ->set('name', 'Bench press')
        ->call('save')
        ->assertHasErrors(['name' => 'unique']);
});
