<?php

use App\Livewire\ExerciseCategories\Edit;
use App\Models\ExerciseCategory;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $exerciseCategory = ExerciseCategory::factory()->create();

    $this->get(route('exercise-categories.edit', $exerciseCategory))->assertRedirect(route('login'));
});

test('edit exercise category page is displayed', function () {
    $this->actingAs(User::factory()->create());

    $exerciseCategory = ExerciseCategory::factory()->create();

    $this->get(route('exercise-categories.edit', $exerciseCategory))->assertOk();
});

test('exercise category name can be updated', function () {
    $this->actingAs(User::factory()->create());

    $exerciseCategory = ExerciseCategory::factory()->create(['name' => 'Mell']);

    Livewire::test(Edit::class, ['exerciseCategory' => $exerciseCategory])
        ->set('name', 'Váll')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('exercise-categories.index'));

    expect($exerciseCategory->refresh()->name)->toBe('Váll');
});

test('exercise category name is required', function () {
    $this->actingAs(User::factory()->create());

    $exerciseCategory = ExerciseCategory::factory()->create();

    Livewire::test(Edit::class, ['exerciseCategory' => $exerciseCategory])
        ->set('name', '')
        ->call('save')
        ->assertHasErrors(['name' => 'required']);
});

test('exercise category name must be unique', function () {
    $this->actingAs(User::factory()->create());

    ExerciseCategory::factory()->create(['name' => 'Mell']);
    $exerciseCategory = ExerciseCategory::factory()->create(['name' => 'Váll']);

    Livewire::test(Edit::class, ['exerciseCategory' => $exerciseCategory])
        ->set('name', 'Mell')
        ->call('save')
        ->assertHasErrors(['name' => 'unique']);
});

test('exercise category can keep its own name unchanged', function () {
    $this->actingAs(User::factory()->create());

    $exerciseCategory = ExerciseCategory::factory()->create(['name' => 'Mell']);

    Livewire::test(Edit::class, ['exerciseCategory' => $exerciseCategory])
        ->set('name', 'Mell')
        ->call('save')
        ->assertHasNoErrors();
});
