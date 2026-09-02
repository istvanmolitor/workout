<?php

use App\Livewire\ExerciseCategories\Create;
use App\Models\ExerciseCategory;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $this->get(route('exercise-categories.create'))->assertRedirect(route('login'));
});

test('create exercise category page is displayed', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('exercise-categories.create'))->assertOk();
});

test('authenticated user can create an exercise category', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(Create::class)
        ->set('name', 'Mell')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('exercise-categories.index'));

    expect(ExerciseCategory::query()->where('name', 'Mell')->exists())->toBeTrue();
});

test('exercise category name is required', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(Create::class)
        ->set('name', '')
        ->call('save')
        ->assertHasErrors(['name' => 'required']);
});

test('exercise category name must be unique', function () {
    $this->actingAs(User::factory()->create());

    ExerciseCategory::factory()->create(['name' => 'Mell']);

    Livewire::test(Create::class)
        ->set('name', 'Mell')
        ->call('save')
        ->assertHasErrors(['name' => 'unique']);
});
