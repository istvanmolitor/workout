<?php

use App\Livewire\Workouts\Perform;
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

test('completed values default to the planned values per set', function () {
    $user = User::factory()->create();
    $workout = Workout::factory()->for($user)->create();
    $exercise = createExerciseWithFields('Bench press', ['Reps', 'Weight']);
    $reps = fieldIdFor($exercise, 'Reps');
    $weight = fieldIdFor($exercise, 'Weight');
    $workoutExercise = WorkoutExercise::factory()->for($workout)->create(['exercise_id' => $exercise->id]);
    $set = WorkoutExerciseSet::factory()->for($workoutExercise, 'workoutExercise')->create();
    $set->values()->create(['field_id' => $reps, 'value' => 8]);
    $set->values()->create(['field_id' => $weight, 'value' => 60]);

    $this->actingAs($user);

    Livewire::test(Perform::class, ['workout' => $workout])
        ->assertSet("exercises.{$workoutExercise->id}.sets.{$set->id}.values.{$reps}.completed_value", '8.00')
        ->assertSet("exercises.{$workoutExercise->id}.sets.{$set->id}.values.{$weight}.completed_value", '60.00')
        ->assertSet("logged.{$workoutExercise->id}", false);
});

test('selecting an exercise shows only that exercise', function () {
    $user = User::factory()->create();
    $workout = Workout::factory()->for($user)->create();
    $benchPressExercise = createExerciseWithFields('Bench press', ['Reps']);
    $benchPress = WorkoutExercise::factory()->for($workout)->create(['exercise_id' => $benchPressExercise->id]);
    WorkoutExerciseSet::factory()->for($benchPress, 'workoutExercise')->create();
    $shoulderPressExercise = createExerciseWithFields('Shoulder press', ['Reps']);
    $shoulderPress = WorkoutExercise::factory()->for($workout)->create(['exercise_id' => $shoulderPressExercise->id]);
    WorkoutExerciseSet::factory()->for($shoulderPress, 'workoutExercise')->create();

    $this->actingAs($user);

    Livewire::test(Perform::class, ['workout' => $workout])
        ->call('selectExercise', $benchPress->id)
        ->assertSee('Bench press')
        ->assertDontSee('Shoulder press');
});

test('owner can log completed values, difficulty and a note for the active exercise', function () {
    $user = User::factory()->create();
    $workout = Workout::factory()->for($user)->create();
    $exerciseCatalog = createExerciseWithFields('Bench press', ['Reps', 'Weight']);
    $reps = fieldIdFor($exerciseCatalog, 'Reps');
    $weight = fieldIdFor($exerciseCatalog, 'Weight');
    $exercise = WorkoutExercise::factory()->for($workout)->create(['exercise_id' => $exerciseCatalog->id]);
    $firstSet = WorkoutExerciseSet::factory()->for($exercise, 'workoutExercise')->create(['order' => 0]);
    $firstSet->values()->create(['field_id' => $reps, 'value' => 8]);
    $firstSet->values()->create(['field_id' => $weight, 'value' => 40]);
    $secondSet = WorkoutExerciseSet::factory()->for($exercise, 'workoutExercise')->create(['order' => 1]);
    $secondSet->values()->create(['field_id' => $reps, 'value' => 6]);
    $secondSet->values()->create(['field_id' => $weight, 'value' => 45]);

    $this->actingAs($user);

    Livewire::test(Perform::class, ['workout' => $workout])
        ->call('selectExercise', $exercise->id)
        ->set("exercises.{$exercise->id}.sets.{$firstSet->id}.values.{$reps}.completed_value", 7)
        ->set("exercises.{$exercise->id}.sets.{$firstSet->id}.values.{$weight}.completed_value", 42.5)
        ->set("exercises.{$exercise->id}.sets.{$secondSet->id}.values.{$reps}.completed_value", 5)
        ->set("exercises.{$exercise->id}.sets.{$secondSet->id}.values.{$weight}.completed_value", 47.5)
        ->set("exercises.{$exercise->id}.difficulty", 4)
        ->set("exercises.{$exercise->id}.note", 'Left knee hurt on the last set.')
        ->call('save')
        ->assertHasNoErrors()
        ->assertSet('activeExerciseId', null)
        ->assertSet("logged.{$exercise->id}", true);

    $exercise->refresh();

    expect((int) $firstSet->values()->where('field_id', $reps)->first()->completed_value)->toBe(7);
    expect((float) $firstSet->values()->where('field_id', $weight)->first()->completed_value)->toBe(42.5);
    expect((int) $secondSet->values()->where('field_id', $reps)->first()->completed_value)->toBe(5);
    expect((float) $secondSet->values()->where('field_id', $weight)->first()->completed_value)->toBe(47.5);
    expect($exercise->difficulty)->toBe(4);
    expect($exercise->note)->toBe('Left knee hurt on the last set.');
});

test('difficulty must be between 1 and 5', function () {
    $user = User::factory()->create();
    $workout = Workout::factory()->for($user)->create();
    $exerciseCatalog = createExerciseWithFields('Bench press', ['Reps']);
    $exercise = WorkoutExercise::factory()->for($workout)->create(['exercise_id' => $exerciseCatalog->id]);
    WorkoutExerciseSet::factory()->for($exercise, 'workoutExercise')->create();

    $this->actingAs($user);

    Livewire::test(Perform::class, ['workout' => $workout])
        ->call('selectExercise', $exercise->id)
        ->set("exercises.{$exercise->id}.difficulty", 6)
        ->call('save')
        ->assertHasErrors(["exercises.{$exercise->id}.difficulty" => 'max']);
});

test('note must not exceed 1000 characters', function () {
    $user = User::factory()->create();
    $workout = Workout::factory()->for($user)->create();
    $exerciseCatalog = createExerciseWithFields('Bench press', ['Reps']);
    $exercise = WorkoutExercise::factory()->for($workout)->create(['exercise_id' => $exerciseCatalog->id]);
    WorkoutExerciseSet::factory()->for($exercise, 'workoutExercise')->create();

    $this->actingAs($user);

    Livewire::test(Perform::class, ['workout' => $workout])
        ->call('selectExercise', $exercise->id)
        ->set("exercises.{$exercise->id}.note", str_repeat('a', 1001))
        ->call('save')
        ->assertHasErrors(["exercises.{$exercise->id}.note" => 'max']);
});
