<?php

use App\Livewire\Workouts\Perform;
use App\Models\Exercise;
use App\Models\User;
use App\Models\Workout;
use App\Models\WorkoutExercise;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $workout = Workout::factory()->create();

    $this->get(route('workouts.perform', $workout))->assertRedirect(route('login'));
});

test('owner can view the perform page', function () {
    $user = User::factory()->create();
    $workout = Workout::factory()->for($user)->create();

    $this->actingAs($user);

    $this->get(route('workouts.perform', $workout))->assertOk();
});

test('a user cannot perform another user\'s workout', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $workout = Workout::factory()->for($owner)->create();

    $this->actingAs($otherUser);

    $this->get(route('workouts.perform', $workout))->assertForbidden();
});

test('completed sets and reps default to the planned values', function () {
    $user = User::factory()->create();
    $workout = Workout::factory()->for($user)->create();
    $exercise = WorkoutExercise::factory()->for($workout)->create([
        'exercise_id' => Exercise::factory()->create(),
        'sets' => 4,
        'reps' => 8,
        'completed_sets' => null,
        'completed_reps' => null,
    ]);

    $this->actingAs($user);

    Livewire::test(Perform::class, ['workout' => $workout])
        ->assertSet("exercises.{$exercise->id}.completed_sets", 4)
        ->assertSet("exercises.{$exercise->id}.completed_reps", 8)
        ->assertSet("logged.{$exercise->id}", false);
});

test('selecting an exercise shows only that exercise', function () {
    $user = User::factory()->create();
    $workout = Workout::factory()->for($user)->create();
    $benchPress = WorkoutExercise::factory()->for($workout)->create([
        'exercise_id' => Exercise::factory()->create(['name' => 'Bench press']),
    ]);
    WorkoutExercise::factory()->for($workout)->create([
        'exercise_id' => Exercise::factory()->create(['name' => 'Shoulder press']),
    ]);

    $this->actingAs($user);

    Livewire::test(Perform::class, ['workout' => $workout])
        ->call('selectExercise', $benchPress->id)
        ->assertSee('Bench press')
        ->assertDontSee('Shoulder press');
});

test('owner can log completed sets, reps and difficulty for the active exercise', function () {
    $user = User::factory()->create();
    $workout = Workout::factory()->for($user)->create();
    $exercise = WorkoutExercise::factory()->for($workout)->create([
        'exercise_id' => Exercise::factory()->create(),
    ]);

    $this->actingAs($user);

    Livewire::test(Perform::class, ['workout' => $workout])
        ->call('selectExercise', $exercise->id)
        ->set("exercises.{$exercise->id}.completed_sets", 3)
        ->set("exercises.{$exercise->id}.completed_reps", 6)
        ->set("exercises.{$exercise->id}.difficulty", 8)
        ->call('save')
        ->assertHasNoErrors()
        ->assertSet('activeExerciseId', null)
        ->assertSet("logged.{$exercise->id}", true);

    $exercise->refresh();

    expect($exercise->completed_sets)->toBe(3);
    expect($exercise->completed_reps)->toBe(6);
    expect($exercise->difficulty)->toBe(8);
});

test('difficulty must be between 1 and 10', function () {
    $user = User::factory()->create();
    $workout = Workout::factory()->for($user)->create();
    $exercise = WorkoutExercise::factory()->for($workout)->create([
        'exercise_id' => Exercise::factory()->create(),
    ]);

    $this->actingAs($user);

    Livewire::test(Perform::class, ['workout' => $workout])
        ->call('selectExercise', $exercise->id)
        ->set("exercises.{$exercise->id}.difficulty", 11)
        ->call('save')
        ->assertHasErrors(["exercises.{$exercise->id}.difficulty" => 'max']);
});
