<?php

use App\Models\User;
use App\Models\WorkoutPlan;
use App\Policies\WorkoutPlanPolicy;

test('owner can update their workout plan', function () {
    $user = User::factory()->create();
    $workoutPlan = WorkoutPlan::factory()->for($user)->create();

    expect((new WorkoutPlanPolicy)->update($user, $workoutPlan))->toBeTrue();
});

test('non-owner cannot update the workout plan', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $workoutPlan = WorkoutPlan::factory()->for($otherUser)->create();

    expect((new WorkoutPlanPolicy)->update($user, $workoutPlan))->toBeFalse();
});

test('owner can view their workout plan', function () {
    $user = User::factory()->create();
    $workoutPlan = WorkoutPlan::factory()->for($user)->create();

    expect((new WorkoutPlanPolicy)->view($user, $workoutPlan))->toBeTrue();
});

test('non-owner cannot view the workout plan', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $workoutPlan = WorkoutPlan::factory()->for($otherUser)->create();

    expect((new WorkoutPlanPolicy)->view($user, $workoutPlan))->toBeFalse();
});
