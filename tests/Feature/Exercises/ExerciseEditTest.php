<?php

use App\Livewire\Exercises\Edit;
use App\Models\Exercise;
use App\Models\ExerciseCategory;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $exercise = Exercise::factory()->create();

    $this->get(route('exercises.edit', $exercise))->assertRedirect(route('login'));
});

test('edit exercise page is displayed', function () {
    $this->actingAs(User::factory()->create());

    $exercise = Exercise::factory()->create();

    $this->get(route('exercises.edit', $exercise))->assertOk();
});

test('exercise name can be updated', function () {
    $this->actingAs(User::factory()->create());

    $exercise = Exercise::factory()->create(['name' => 'Bench press']);

    Livewire::test(Edit::class, ['exercise' => $exercise])
        ->set('name', 'Incline bench press')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('exercises.index'));

    expect($exercise->refresh()->name)->toBe('Incline bench press');
});

test('exercise name is required', function () {
    $this->actingAs(User::factory()->create());

    $exercise = Exercise::factory()->create();

    Livewire::test(Edit::class, ['exercise' => $exercise])
        ->set('name', '')
        ->call('save')
        ->assertHasErrors(['name' => 'required']);
});

test('exercise name must be unique', function () {
    $this->actingAs(User::factory()->create());

    Exercise::factory()->create(['name' => 'Bench press']);
    $exercise = Exercise::factory()->create(['name' => 'Lat pulldown']);

    Livewire::test(Edit::class, ['exercise' => $exercise])
        ->set('name', 'Bench press')
        ->call('save')
        ->assertHasErrors(['name' => 'unique']);
});

test('exercise can keep its own name unchanged', function () {
    $this->actingAs(User::factory()->create());

    $exercise = Exercise::factory()->create(['name' => 'Bench press']);

    Livewire::test(Edit::class, ['exercise' => $exercise])
        ->set('name', 'Bench press')
        ->call('save')
        ->assertHasNoErrors();
});

test('exercise category is required', function () {
    $this->actingAs(User::factory()->create());

    $exercise = Exercise::factory()->create();

    Livewire::test(Edit::class, ['exercise' => $exercise])
        ->set('category_id', null)
        ->call('save')
        ->assertHasErrors(['category_id' => 'required']);
});

test('exercise category can be updated', function () {
    $this->actingAs(User::factory()->create());

    $exercise = Exercise::factory()->create();
    $category = ExerciseCategory::factory()->create();

    Livewire::test(Edit::class, ['exercise' => $exercise])
        ->set('category_id', $category->id)
        ->call('save')
        ->assertHasNoErrors();

    expect($exercise->refresh()->category_id)->toBe($category->id);
});
