<?php

use App\Models\User;
use App\Models\Workout;
use App\Policies\WorkoutPolicy;

test('owner can update their workout', function () {
    $user = User::factory()->create();
    $workout = Workout::factory()->for($user)->create();

    expect((new WorkoutPolicy)->update($user, $workout))->toBeTrue();
});

test('non-owner cannot update the workout', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $workout = Workout::factory()->for($otherUser)->create();

    expect((new WorkoutPolicy)->update($user, $workout))->toBeFalse();
});
