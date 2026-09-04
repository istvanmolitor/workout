<?php

use App\Livewire\WorkoutPlans\Create;
use App\Models\User;
use App\Models\WorkoutPlan;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $this->get(route('workout-plans.create'))->assertRedirect(route('login'));
});

test('create workout plan page is displayed', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('workout-plans.create'))->assertOk();
});

test('authenticated user can create a workout plan with exercises and varying values per set', function () {
    $user = User::factory()->create();
    $benchPress = createExerciseWithFields('Bench press', ['Reps', 'Weight']);
    $reps = fieldIdFor($benchPress, 'Reps');
    $weight = fieldIdFor($benchPress, 'Weight');
    $shoulderPress = createExerciseWithFields('Shoulder press', ['Reps', 'Weight']);
    $shoulderReps = fieldIdFor($shoulderPress, 'Reps');

    $this->actingAs($user);

    Livewire::test(Create::class)
        ->set('name', 'Push day')
        ->set('description', 'Chest, shoulders and triceps')
        ->set('exercises', [
            ['exercise_id' => $benchPress->id, 'sets' => [
                ['values' => [$reps => 12, $weight => 60]],
                ['values' => [$reps => 10, $weight => 65]],
                ['values' => [$reps => 8, $weight => 70]],
            ]],
            ['exercise_id' => $shoulderPress->id, 'sets' => [
                ['values' => [$shoulderReps => 10]],
                ['values' => [$shoulderReps => 10]],
                ['values' => [$shoulderReps => 10]],
            ]],
        ])
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('workout-plans.index'));

    $workoutPlan = WorkoutPlan::query()->where('user_id', $user->id)->sole();

    expect($workoutPlan->name)->toBe('Push day');
    expect($workoutPlan->description)->toBe('Chest, shoulders and triceps');
    expect($workoutPlan->exercises)->toHaveCount(2);
    expect($workoutPlan->exercises->first()->exercise->name)->toBe('Bench press');
    expect($workoutPlan->exercises->first()->sets->map(fn ($set) => $set->values->firstWhere('field_id', $reps)->value)->map(fn ($value) => (int) $value)->all())->toBe([12, 10, 8]);
    expect($workoutPlan->exercises->first()->sets->map(fn ($set) => (float) $set->values->firstWhere('field_id', $weight)->value)->all())->toBe([60.0, 65.0, 70.0]);
    expect($workoutPlan->exercises->last()->sets->first()->values)->toHaveCount(1);
});

test('workout plan name is required', function () {
    $this->actingAs(User::factory()->create());
    $exercise = createExerciseWithFields('Squat', ['Reps']);
    $reps = fieldIdFor($exercise, 'Reps');

    Livewire::test(Create::class)
        ->set('name', '')
        ->set('exercises', [['exercise_id' => $exercise->id, 'sets' => [['values' => [$reps => 8]]]]])
        ->call('save')
        ->assertHasErrors(['name' => 'required']);
});

test('at least one exercise is required', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(Create::class)
        ->set('name', 'Push day')
        ->set('exercises', [])
        ->call('save')
        ->assertHasErrors(['exercises' => 'required']);
});

test('exercise selection is required', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(Create::class)
        ->set('name', 'Push day')
        ->set('exercises', [['exercise_id' => '', 'sets' => [['values' => []]]]])
        ->call('save')
        ->assertHasErrors(['exercises.0.exercise_id' => 'required']);
});

test('at least one set is required per exercise', function () {
    $this->actingAs(User::factory()->create());
    $exercise = createExerciseWithFields('Squat', ['Reps']);

    Livewire::test(Create::class)
        ->set('name', 'Push day')
        ->set('exercises', [['exercise_id' => $exercise->id, 'sets' => []]])
        ->call('save')
        ->assertHasErrors(['exercises.0.sets' => 'required']);
});

test('set field value must be a non-negative number', function () {
    $this->actingAs(User::factory()->create());
    $exercise = createExerciseWithFields('Squat', ['Reps']);
    $reps = fieldIdFor($exercise, 'Reps');

    Livewire::test(Create::class)
        ->set('name', 'Push day')
        ->set('exercises', [['exercise_id' => $exercise->id, 'sets' => [['values' => [$reps => -5]]]]])
        ->call('save')
        ->assertHasErrors(["exercises.0.sets.0.values.{$reps}" => 'min']);
});

test('selecting a single-set exercise trims existing sets to one', function () {
    $this->actingAs(User::factory()->create());
    $exercise = createExerciseWithFields('Plank', ['Idő'], singleSet: true);

    Livewire::test(Create::class)
        ->set('exercises.0.exercise_id', $exercise->id)
        ->assertCount('exercises.0.sets', 1);
});

test('a single-set exercise type can be saved with exactly one set', function () {
    $user = User::factory()->create();
    $exercise = createExerciseWithFields('Plank', ['Idő'], singleSet: true);
    $time = fieldIdFor($exercise, 'Idő');

    $this->actingAs($user);

    Livewire::test(Create::class)
        ->set('name', 'Core nap')
        ->set('exercises', [['exercise_id' => $exercise->id, 'sets' => [['values' => [$time => 60]]]]])
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('workout-plans.index'));

    $workoutPlan = WorkoutPlan::query()->where('user_id', $user->id)->sole();

    expect($workoutPlan->exercises->first()->sets)->toHaveCount(1);
});

test('a single-set exercise type rejects more than one set', function () {
    $this->actingAs(User::factory()->create());
    $exercise = createExerciseWithFields('Plank', ['Idő'], singleSet: true);
    $time = fieldIdFor($exercise, 'Idő');

    Livewire::test(Create::class)
        ->set('name', 'Core nap')
        ->set('exercises', [
            ['exercise_id' => $exercise->id, 'sets' => [
                ['values' => [$time => 30]],
                ['values' => [$time => 45]],
            ]],
        ])
        ->call('save')
        ->assertHasErrors(['exercises.0.sets']);

    expect(WorkoutPlan::query()->count())->toBe(0);
});
