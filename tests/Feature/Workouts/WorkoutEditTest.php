<?php

use App\Livewire\Workouts\Edit;
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

test('owner can log completed values and difficulty per set', function () {
    $user = User::factory()->create();
    $workout = Workout::factory()->for($user)->create();
    $exerciseCatalog = createExerciseWithFields('Bench press', ['Reps', 'Weight']);
    $reps = fieldIdFor($exerciseCatalog, 'Reps');
    $weight = fieldIdFor($exerciseCatalog, 'Weight');
    $exercise = WorkoutExercise::factory()->for($workout)->create(['exercise_id' => $exerciseCatalog->id]);
    $firstSet = WorkoutExerciseSet::factory()->for($exercise, 'workoutExercise')->create(['order' => 0]);
    $firstSet->values()->create(['field_id' => $reps, 'value' => 12]);
    $firstSet->values()->create(['field_id' => $weight, 'value' => 60]);
    $secondSet = WorkoutExerciseSet::factory()->for($exercise, 'workoutExercise')->create(['order' => 1]);
    $secondSet->values()->create(['field_id' => $reps, 'value' => 8]);
    $secondSet->values()->create(['field_id' => $weight, 'value' => 70]);

    $this->actingAs($user);

    Livewire::test(Edit::class, ['workout' => $workout])
        ->set("exercises.{$exercise->id}.sets.{$firstSet->id}.values.{$reps}.completed_value", 10)
        ->set("exercises.{$exercise->id}.sets.{$firstSet->id}.values.{$weight}.completed_value", 62.5)
        ->set("exercises.{$exercise->id}.sets.{$secondSet->id}.values.{$reps}.completed_value", 6)
        ->set("exercises.{$exercise->id}.sets.{$secondSet->id}.values.{$weight}.completed_value", 72.5)
        ->set("exercises.{$exercise->id}.difficulty", 4)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('workouts.index'));

    $exercise->refresh();

    expect((int) $firstSet->values()->where('field_id', $reps)->first()->completed_value)->toBe(10);
    expect((float) $firstSet->values()->where('field_id', $weight)->first()->completed_value)->toBe(62.5);
    expect((int) $secondSet->values()->where('field_id', $reps)->first()->completed_value)->toBe(6);
    expect((float) $secondSet->values()->where('field_id', $weight)->first()->completed_value)->toBe(72.5);
    expect($exercise->difficulty)->toBe(4);
});

test('difficulty must be between 1 and 5', function () {
    $user = User::factory()->create();
    $workout = Workout::factory()->for($user)->create();
    $exerciseCatalog = createExerciseWithFields('Bench press', ['Reps']);
    $exercise = WorkoutExercise::factory()->for($workout)->create(['exercise_id' => $exerciseCatalog->id]);
    WorkoutExerciseSet::factory()->for($exercise, 'workoutExercise')->create();

    $this->actingAs($user);

    Livewire::test(Edit::class, ['workout' => $workout])
        ->set("exercises.{$exercise->id}.difficulty", 6)
        ->call('save')
        ->assertHasErrors(["exercises.{$exercise->id}.difficulty" => 'max']);
});
