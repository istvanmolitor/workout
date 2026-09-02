<?php

use App\Livewire\WorkoutPlans\Create;
use App\Models\Exercise;
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

test('authenticated user can create a workout plan with exercises and varying reps and weight per set', function () {
    $user = User::factory()->create();
    $benchPress = Exercise::factory()->create(['name' => 'Bench press']);
    $shoulderPress = Exercise::factory()->create(['name' => 'Shoulder press']);

    $this->actingAs($user);

    Livewire::test(Create::class)
        ->set('name', 'Push day')
        ->set('description', 'Chest, shoulders and triceps')
        ->set('exercises', [
            ['exercise_id' => $benchPress->id, 'sets' => [['reps' => 12, 'weight' => 60], ['reps' => 10, 'weight' => 65], ['reps' => 8, 'weight' => 70]]],
            ['exercise_id' => $shoulderPress->id, 'sets' => [['reps' => 10, 'weight' => null], ['reps' => 10, 'weight' => null], ['reps' => 10, 'weight' => null]]],
        ])
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('workout-plans.index'));

    $workoutPlan = WorkoutPlan::query()->where('user_id', $user->id)->sole();

    expect($workoutPlan->name)->toBe('Push day');
    expect($workoutPlan->description)->toBe('Chest, shoulders and triceps');
    expect($workoutPlan->exercises)->toHaveCount(2);
    expect($workoutPlan->exercises->first()->exercise->name)->toBe('Bench press');
    expect($workoutPlan->exercises->first()->sets->pluck('reps')->all())->toBe([12, 10, 8]);
    expect($workoutPlan->exercises->first()->sets->pluck('weight')->map(fn ($weight) => (float) $weight)->all())->toBe([60.0, 65.0, 70.0]);
    expect($workoutPlan->exercises->last()->sets->pluck('weight')->filter()->all())->toBe([]);
});

test('workout plan name is required', function () {
    $this->actingAs(User::factory()->create());
    $exercise = Exercise::factory()->create();

    Livewire::test(Create::class)
        ->set('name', '')
        ->set('exercises', [['exercise_id' => $exercise->id, 'sets' => [['reps' => 8, 'weight' => null]]]])
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
        ->set('exercises', [['exercise_id' => '', 'sets' => [['reps' => 8, 'weight' => null]]]])
        ->call('save')
        ->assertHasErrors(['exercises.0.exercise_id' => 'required']);
});

test('at least one set is required per exercise', function () {
    $this->actingAs(User::factory()->create());
    $exercise = Exercise::factory()->create();

    Livewire::test(Create::class)
        ->set('name', 'Push day')
        ->set('exercises', [['exercise_id' => $exercise->id, 'sets' => []]])
        ->call('save')
        ->assertHasErrors(['exercises.0.sets' => 'required']);
});

test('set weight must be a non-negative number', function () {
    $this->actingAs(User::factory()->create());
    $exercise = Exercise::factory()->create();

    Livewire::test(Create::class)
        ->set('name', 'Push day')
        ->set('exercises', [['exercise_id' => $exercise->id, 'sets' => [['reps' => 8, 'weight' => -5]]]])
        ->call('save')
        ->assertHasErrors(['exercises.0.sets.0.weight' => 'min']);
});
