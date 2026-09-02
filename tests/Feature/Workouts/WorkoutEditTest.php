<?php

use App\Livewire\Workouts\Edit;
use App\Models\Exercise;
use App\Models\User;
use App\Models\Workout;
use App\Models\WorkoutExercise;
use App\Models\WorkoutExerciseSet;
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

test('owner can log completed reps, weight and difficulty per set', function () {
    $user = User::factory()->create();
    $workout = Workout::factory()->for($user)->create();
    $exercise = WorkoutExercise::factory()->for($workout)->create([
        'exercise_id' => Exercise::factory()->create(),
    ]);
    $firstSet = WorkoutExerciseSet::factory()->for($exercise, 'workoutExercise')->create(['reps' => 12, 'weight' => 60, 'order' => 0]);
    $secondSet = WorkoutExerciseSet::factory()->for($exercise, 'workoutExercise')->create(['reps' => 8, 'weight' => 70, 'order' => 1]);

    $this->actingAs($user);

    Livewire::test(Edit::class, ['workout' => $workout])
        ->set("exercises.{$exercise->id}.sets.{$firstSet->id}.completed_reps", 10)
        ->set("exercises.{$exercise->id}.sets.{$firstSet->id}.completed_weight", 62.5)
        ->set("exercises.{$exercise->id}.sets.{$secondSet->id}.completed_reps", 6)
        ->set("exercises.{$exercise->id}.sets.{$secondSet->id}.completed_weight", 72.5)
        ->set("exercises.{$exercise->id}.difficulty", 7)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('workouts.index'));

    $exercise->refresh();

    expect($firstSet->refresh()->completed_reps)->toBe(10);
    expect((float) $firstSet->completed_weight)->toBe(62.5);
    expect($secondSet->refresh()->completed_reps)->toBe(6);
    expect((float) $secondSet->completed_weight)->toBe(72.5);
    expect($exercise->difficulty)->toBe(7);
});

test('difficulty must be between 1 and 10', function () {
    $user = User::factory()->create();
    $workout = Workout::factory()->for($user)->create();
    $exercise = WorkoutExercise::factory()->for($workout)->create();
    WorkoutExerciseSet::factory()->for($exercise, 'workoutExercise')->create();

    $this->actingAs($user);

    Livewire::test(Edit::class, ['workout' => $workout])
        ->set("exercises.{$exercise->id}.difficulty", 11)
        ->call('save')
        ->assertHasErrors(["exercises.{$exercise->id}.difficulty" => 'max']);
});
