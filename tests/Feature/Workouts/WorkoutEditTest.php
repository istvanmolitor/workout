<?php

use App\Livewire\Workouts\Edit;
use App\Models\Exercise;
use App\Models\User;
use App\Models\Workout;
use App\Models\WorkoutExercise;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $workout = Workout::factory()->create();

    $this->get(route('workouts.edit', $workout))->assertRedirect(route('login'));
});

test('owner can view the log page', function () {
    $user = User::factory()->create();
    $workout = Workout::factory()->for($user)->create();

    $this->actingAs($user);

    $this->get(route('workouts.edit', $workout))->assertOk();
});

test('a user cannot view another user\'s workout', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $workout = Workout::factory()->for($owner)->create();

    $this->actingAs($otherUser);

    $this->get(route('workouts.edit', $workout))->assertForbidden();
});

test('owner can log completed sets, reps and difficulty for an exercise', function () {
    $user = User::factory()->create();
    $workout = Workout::factory()->for($user)->create();
    $exercise = WorkoutExercise::factory()->for($workout)->create([
        'exercise_id' => Exercise::factory()->create(),
        'sets' => 4,
        'reps' => 8,
    ]);

    $this->actingAs($user);

    Livewire::test(Edit::class, ['workout' => $workout])
        ->set("exercises.{$exercise->id}.completed_sets", 3)
        ->set("exercises.{$exercise->id}.completed_reps", 8)
        ->set("exercises.{$exercise->id}.difficulty", 7)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('workouts.index'));

    $exercise->refresh();

    expect($exercise->completed_sets)->toBe(3);
    expect($exercise->completed_reps)->toBe(8);
    expect($exercise->difficulty)->toBe(7);
});

test('difficulty must be between 1 and 10', function () {
    $user = User::factory()->create();
    $workout = Workout::factory()->for($user)->create();
    $exercise = WorkoutExercise::factory()->for($workout)->create();

    $this->actingAs($user);

    Livewire::test(Edit::class, ['workout' => $workout])
        ->set("exercises.{$exercise->id}.difficulty", 11)
        ->call('save')
        ->assertHasErrors(["exercises.{$exercise->id}.difficulty" => 'max']);
});
