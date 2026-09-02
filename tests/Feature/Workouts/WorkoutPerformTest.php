<?php

use App\Livewire\Workouts\Perform;
use App\Models\Exercise;
use App\Models\User;
use App\Models\Workout;
use App\Models\WorkoutExercise;
use App\Models\WorkoutExerciseSet;
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

test('completed reps and weight default to the planned values per set', function () {
    $user = User::factory()->create();
    $workout = Workout::factory()->for($user)->create();
    $exercise = WorkoutExercise::factory()->for($workout)->create([
        'exercise_id' => Exercise::factory()->create(),
    ]);
    $set = WorkoutExerciseSet::factory()->for($exercise, 'workoutExercise')->create([
        'reps' => 8,
        'completed_reps' => null,
        'weight' => 60,
        'completed_weight' => null,
    ]);

    $this->actingAs($user);

    Livewire::test(Perform::class, ['workout' => $workout])
        ->assertSet("exercises.{$exercise->id}.sets.{$set->id}.completed_reps", 8)
        ->assertSet("exercises.{$exercise->id}.sets.{$set->id}.completed_weight", '60.00')
        ->assertSet("logged.{$exercise->id}", false);
});

test('selecting an exercise shows only that exercise', function () {
    $user = User::factory()->create();
    $workout = Workout::factory()->for($user)->create();
    $benchPress = WorkoutExercise::factory()->for($workout)->create([
        'exercise_id' => Exercise::factory()->create(['name' => 'Bench press']),
    ]);
    WorkoutExerciseSet::factory()->for($benchPress, 'workoutExercise')->create();
    $shoulderPress = WorkoutExercise::factory()->for($workout)->create([
        'exercise_id' => Exercise::factory()->create(['name' => 'Shoulder press']),
    ]);
    WorkoutExerciseSet::factory()->for($shoulderPress, 'workoutExercise')->create();

    $this->actingAs($user);

    Livewire::test(Perform::class, ['workout' => $workout])
        ->call('selectExercise', $benchPress->id)
        ->assertSee('Bench press')
        ->assertDontSee('Shoulder press');
});

test('owner can log completed reps, weight and difficulty for the active exercise', function () {
    $user = User::factory()->create();
    $workout = Workout::factory()->for($user)->create();
    $exercise = WorkoutExercise::factory()->for($workout)->create([
        'exercise_id' => Exercise::factory()->create(),
    ]);
    $firstSet = WorkoutExerciseSet::factory()->for($exercise, 'workoutExercise')->create(['reps' => 8, 'weight' => 40, 'order' => 0]);
    $secondSet = WorkoutExerciseSet::factory()->for($exercise, 'workoutExercise')->create(['reps' => 6, 'weight' => 45, 'order' => 1]);

    $this->actingAs($user);

    Livewire::test(Perform::class, ['workout' => $workout])
        ->call('selectExercise', $exercise->id)
        ->set("exercises.{$exercise->id}.sets.{$firstSet->id}.completed_reps", 7)
        ->set("exercises.{$exercise->id}.sets.{$firstSet->id}.completed_weight", 42.5)
        ->set("exercises.{$exercise->id}.sets.{$secondSet->id}.completed_reps", 5)
        ->set("exercises.{$exercise->id}.sets.{$secondSet->id}.completed_weight", 47.5)
        ->set("exercises.{$exercise->id}.difficulty", 8)
        ->call('save')
        ->assertHasNoErrors()
        ->assertSet('activeExerciseId', null)
        ->assertSet("logged.{$exercise->id}", true);

    $exercise->refresh();

    expect($firstSet->refresh()->completed_reps)->toBe(7);
    expect((float) $firstSet->completed_weight)->toBe(42.5);
    expect($secondSet->refresh()->completed_reps)->toBe(5);
    expect((float) $secondSet->completed_weight)->toBe(47.5);
    expect($exercise->difficulty)->toBe(8);
});

test('difficulty must be between 1 and 10', function () {
    $user = User::factory()->create();
    $workout = Workout::factory()->for($user)->create();
    $exercise = WorkoutExercise::factory()->for($workout)->create([
        'exercise_id' => Exercise::factory()->create(),
    ]);
    WorkoutExerciseSet::factory()->for($exercise, 'workoutExercise')->create();

    $this->actingAs($user);

    Livewire::test(Perform::class, ['workout' => $workout])
        ->call('selectExercise', $exercise->id)
        ->set("exercises.{$exercise->id}.difficulty", 11)
        ->call('save')
        ->assertHasErrors(["exercises.{$exercise->id}.difficulty" => 'max']);
});
