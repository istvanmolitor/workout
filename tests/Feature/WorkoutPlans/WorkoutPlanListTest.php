<?php

use App\Livewire\WorkoutPlans\Manage;
use App\Models\User;
use App\Models\Workout;
use App\Models\WorkoutPlan;
use App\Models\WorkoutPlanExercise;
use App\Models\WorkoutPlanExerciseSet;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $this->get(route('workout-plans.index'))->assertRedirect(route('login'));
});

test('workout plans page is displayed', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('workout-plans.index'))->assertOk();
});

test('user only sees their own workout plans', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    WorkoutPlan::factory()->for($otherUser)->create(['name' => 'Someone else\'s plan']);
    $ownPlan = WorkoutPlan::factory()->for($user)->create(['name' => 'My plan']);

    $this->actingAs($user);

    Livewire::test(Manage::class)
        ->assertSee('My plan')
        ->assertDontSee('Someone else\'s plan');

    expect($ownPlan->user_id)->toBe($user->id);
});

test('owner can start a workout from a workout plan, copying its exercises and sets', function () {
    $user = User::factory()->create();
    $workoutPlan = WorkoutPlan::factory()->for($user)->create(['name' => 'Push day']);
    $benchPress = createExerciseWithFields('Bench press', ['Reps', 'Weight']);
    $reps = fieldIdFor($benchPress, 'Reps');
    $weight = fieldIdFor($benchPress, 'Weight');
    $planExercise = WorkoutPlanExercise::factory()->for($workoutPlan)->create([
        'exercise_id' => $benchPress->id,
        'order' => 0,
    ]);
    $firstSet = WorkoutPlanExerciseSet::factory()->for($planExercise, 'workoutPlanExercise')->create(['order' => 0]);
    $firstSet->values()->create(['field_id' => $reps, 'value' => 12]);
    $firstSet->values()->create(['field_id' => $weight, 'value' => 60]);
    $secondSet = WorkoutPlanExerciseSet::factory()->for($planExercise, 'workoutPlanExercise')->create(['order' => 1]);
    $secondSet->values()->create(['field_id' => $reps, 'value' => 8]);
    $secondSet->values()->create(['field_id' => $weight, 'value' => 70]);

    $this->actingAs($user);

    Livewire::test(Manage::class)
        ->call('startWorkout', $workoutPlan->id)
        ->assertRedirect();

    $workout = Workout::query()->where('user_id', $user->id)->sole();

    expect($workout->workout_plan_id)->toBe($workoutPlan->id);
    expect($workout->name)->toBe('Push day');
    expect($workout->exercises)->toHaveCount(1);
    expect($workout->exercises->first()->exercise_id)->toBe($benchPress->id);
    expect($workout->exercises->first()->sets->map(fn ($set) => (int) $set->values->firstWhere('field_id', $reps)->value)->all())->toBe([12, 8]);
    expect($workout->exercises->first()->sets->map(fn ($set) => $set->values->firstWhere('field_id', $reps)->completed_value)->all())->toBe([null, null]);
    expect($workout->exercises->first()->sets->map(fn ($set) => (float) $set->values->firstWhere('field_id', $weight)->value)->all())->toBe([60.0, 70.0]);
    expect($workout->exercises->first()->sets->map(fn ($set) => $set->values->firstWhere('field_id', $weight)->completed_value)->all())->toBe([null, null]);
});

test('a user cannot start a workout from another user\'s workout plan', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $workoutPlan = WorkoutPlan::factory()->for($owner)->create();

    $this->actingAs($otherUser);

    Livewire::test(Manage::class)
        ->call('startWorkout', $workoutPlan->id)
        ->assertForbidden();

    expect(Workout::query()->count())->toBe(0);
});

test('owner can delete their workout plan', function () {
    $user = User::factory()->create();
    $workoutPlan = WorkoutPlan::factory()->for($user)->create(['name' => 'Push day']);

    $this->actingAs($user);

    Livewire::test(Manage::class)
        ->call('deleteWorkoutPlan', $workoutPlan->id)
        ->assertDontSee('Push day');

    expect(WorkoutPlan::query()->find($workoutPlan->id))->toBeNull();
});

test('deleting a workout plan removes its exercises and sets', function () {
    $user = User::factory()->create();
    $workoutPlan = WorkoutPlan::factory()->for($user)->create();
    $planExercise = WorkoutPlanExercise::factory()->for($workoutPlan)->create();
    WorkoutPlanExerciseSet::factory()->for($planExercise, 'workoutPlanExercise')->create();

    $this->actingAs($user);

    Livewire::test(Manage::class)->call('deleteWorkoutPlan', $workoutPlan->id);

    expect(WorkoutPlanExercise::query()->where('workout_plan_id', $workoutPlan->id)->count())->toBe(0);
});

test('a user cannot delete another user\'s workout plan', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $workoutPlan = WorkoutPlan::factory()->for($owner)->create();

    $this->actingAs($otherUser);

    Livewire::test(Manage::class)
        ->call('deleteWorkoutPlan', $workoutPlan->id)
        ->assertForbidden();

    expect(WorkoutPlan::query()->find($workoutPlan->id))->not->toBeNull();
});
