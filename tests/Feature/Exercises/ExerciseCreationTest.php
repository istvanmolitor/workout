<?php

use App\Livewire\Exercises\Create;
use App\Models\Exercise;
use App\Models\ExerciseCategory;
use App\Models\ExerciseType;
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

    $category = ExerciseCategory::factory()->create();
    $exerciseType = ExerciseType::factory()->create();

    Livewire::test(Create::class)
        ->set('name', 'Bench press')
        ->set('category_id', $category->id)
        ->set('exercise_type_id', $exerciseType->id)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('exercises.index'));

    expect(Exercise::query()->where('name', 'Bench press')->where('category_id', $category->id)->where('exercise_type_id', $exerciseType->id)->exists())->toBeTrue();
});

test('exercise name is required', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(Create::class)
        ->set('name', '')
        ->set('category_id', ExerciseCategory::factory()->create()->id)
        ->set('exercise_type_id', ExerciseType::factory()->create()->id)
        ->call('save')
        ->assertHasErrors(['name' => 'required']);
});

test('exercise name must be unique', function () {
    $this->actingAs(User::factory()->create());

    Exercise::factory()->create(['name' => 'Bench press']);

    Livewire::test(Create::class)
        ->set('name', 'Bench press')
        ->set('category_id', ExerciseCategory::factory()->create()->id)
        ->set('exercise_type_id', ExerciseType::factory()->create()->id)
        ->call('save')
        ->assertHasErrors(['name' => 'unique']);
});

test('exercise category is required', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(Create::class)
        ->set('name', 'Bench press')
        ->set('category_id', null)
        ->set('exercise_type_id', ExerciseType::factory()->create()->id)
        ->call('save')
        ->assertHasErrors(['category_id' => 'required']);
});

test('exercise type is required', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(Create::class)
        ->set('name', 'Bench press')
        ->set('category_id', ExerciseCategory::factory()->create()->id)
        ->set('exercise_type_id', null)
        ->call('save')
        ->assertHasErrors(['exercise_type_id' => 'required']);
});
