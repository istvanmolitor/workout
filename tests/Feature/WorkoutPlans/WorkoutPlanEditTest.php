<?php

use App\Livewire\WorkoutPlans\Edit;
use App\Models\User;
use App\Models\WorkoutPlan;
use App\Models\WorkoutPlanExercise;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $workoutPlan = WorkoutPlan::factory()->create();

    $this->get(route('workout-plans.edit', $workoutPlan))->assertRedirect(route('login'));
});

test('owner can view the edit page', function () {
    $user = User::factory()->create();
    $workoutPlan = WorkoutPlan::factory()->for($user)->create();

    $this->actingAs($user);

    $this->get(route('workout-plans.edit', $workoutPlan))->assertOk();
});

test('a user cannot edit another user\'s workout plan', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $workoutPlan = WorkoutPlan::factory()->for($owner)->create();

    $this->actingAs($otherUser);

    $this->get(route('workout-plans.edit', $workoutPlan))->assertForbidden();
});

test('owner can update the workout plan and its exercises', function () {
    $user = User::factory()->create();
    $workoutPlan = WorkoutPlan::factory()->for($user)->create(['name' => 'Push day']);
    WorkoutPlanExercise::factory()->for($workoutPlan)->create(['name' => 'Old exercise']);

    $this->actingAs($user);

    Livewire::test(Edit::class, ['workoutPlan' => $workoutPlan])
        ->set('name', 'Push day (updated)')
        ->set('exercises', [
            ['name' => 'Bench press', 'sets' => 4, 'reps' => 8],
        ])
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('workout-plans.index'));

    $workoutPlan->refresh();

    expect($workoutPlan->name)->toBe('Push day (updated)');
    expect($workoutPlan->exercises)->toHaveCount(1);
    expect($workoutPlan->exercises->first()->name)->toBe('Bench press');
});

test('workout plan name is required', function () {
    $user = User::factory()->create();
    $workoutPlan = WorkoutPlan::factory()->for($user)->create();
    WorkoutPlanExercise::factory()->for($workoutPlan)->create();

    $this->actingAs($user);

    Livewire::test(Edit::class, ['workoutPlan' => $workoutPlan])
        ->set('name', '')
        ->call('save')
        ->assertHasErrors(['name' => 'required']);
});
