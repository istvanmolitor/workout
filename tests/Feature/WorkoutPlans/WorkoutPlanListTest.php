<?php

use App\Livewire\WorkoutPlans\Manage;
use App\Models\Exercise;
use App\Models\User;
use App\Models\Workout;
use App\Models\WorkoutPlan;
use App\Models\WorkoutPlanExercise;
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

test('owner can start a workout from a workout plan, copying its exercises', function () {
    $user = User::factory()->create();
    $workoutPlan = WorkoutPlan::factory()->for($user)->create(['name' => 'Push day']);
    $benchPress = Exercise::factory()->create(['name' => 'Bench press']);
    WorkoutPlanExercise::factory()->for($workoutPlan)->create([
        'exercise_id' => $benchPress->id,
        'sets' => 4,
        'reps' => 8,
        'order' => 0,
    ]);

    $this->actingAs($user);

    Livewire::test(Manage::class)
        ->call('startWorkout', $workoutPlan->id)
        ->assertRedirect();

    $workout = Workout::query()->where('user_id', $user->id)->sole();

    expect($workout->workout_plan_id)->toBe($workoutPlan->id);
    expect($workout->name)->toBe('Push day');
    expect($workout->exercises)->toHaveCount(1);
    expect($workout->exercises->first()->exercise_id)->toBe($benchPress->id);
    expect($workout->exercises->first()->sets)->toBe(4);
    expect($workout->exercises->first()->reps)->toBe(8);
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
