<?php

use App\Livewire\Exercises\Manage;
use App\Models\Exercise;
use App\Models\User;
use App\Models\WorkoutPlan;
use App\Models\WorkoutPlanExercise;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $this->get(route('exercises.index'))->assertRedirect(route('login'));
});

test('exercises page is displayed', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('exercises.index'))->assertOk();
});

test('lists exercises in the catalog', function () {
    $this->actingAs(User::factory()->create());

    Exercise::factory()->create(['name' => 'Bench press']);
    Exercise::factory()->create(['name' => 'Lat pulldown']);

    Livewire::test(Manage::class)
        ->assertSee('Bench press')
        ->assertSee('Lat pulldown');
});

test('an unused exercise can be deleted', function () {
    $this->actingAs(User::factory()->create());

    $exercise = Exercise::factory()->create();

    Livewire::test(Manage::class)
        ->call('delete', $exercise->id);

    expect(Exercise::query()->find($exercise->id))->toBeNull();
});

test('an exercise used in a workout plan cannot be deleted', function () {
    $this->actingAs(User::factory()->create());

    $exercise = Exercise::factory()->create();
    WorkoutPlanExercise::factory()->for(WorkoutPlan::factory())->create(['exercise_id' => $exercise->id]);

    Livewire::test(Manage::class)
        ->call('delete', $exercise->id);

    expect(Exercise::query()->find($exercise->id))->not->toBeNull();
});
