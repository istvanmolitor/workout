<?php

use App\Livewire\ExerciseCategories\Manage;
use App\Models\Exercise;
use App\Models\ExerciseCategory;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $this->get(route('exercise-categories.index'))->assertRedirect(route('login'));
});

test('exercise categories page is displayed', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('exercise-categories.index'))->assertOk();
});

test('lists exercise categories', function () {
    $this->actingAs(User::factory()->create());

    ExerciseCategory::factory()->create(['name' => 'Mell']);
    ExerciseCategory::factory()->create(['name' => 'Váll']);

    Livewire::test(Manage::class)
        ->assertSee('Mell')
        ->assertSee('Váll');
});

test('an exercise category can be deleted', function () {
    $this->actingAs(User::factory()->create());

    $exerciseCategory = ExerciseCategory::factory()->create();

    Livewire::test(Manage::class)
        ->call('delete', $exerciseCategory->id);

    expect(ExerciseCategory::query()->find($exerciseCategory->id))->toBeNull();
});

test('deleting a category used by exercises unassigns it from them', function () {
    $this->actingAs(User::factory()->create());

    $exerciseCategory = ExerciseCategory::factory()->create();
    $exercise = Exercise::factory()->create(['category_id' => $exerciseCategory->id]);

    Livewire::test(Manage::class)
        ->call('delete', $exerciseCategory->id);

    expect($exercise->refresh()->category_id)->toBeNull();
});
