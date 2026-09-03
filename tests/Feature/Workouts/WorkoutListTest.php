<?php

use App\Livewire\Workouts\Manage;
use App\Models\User;
use App\Models\Workout;
use App\Models\WorkoutExercise;
use App\Models\WorkoutExerciseSet;
use App\Models\WorkoutPlan;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $this->get(route('workouts.index'))->assertRedirect(route('login'));
});

test('workouts page is displayed', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('workouts.index'))->assertOk();
});

test('workouts page has a link to the calendar', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(Manage::class)->assertSeeHtml(route('workouts.calendar'));
});

test('user only sees their own workouts', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    Workout::factory()->for($otherUser)->create(['name' => 'Someone else\'s workout']);
    $ownWorkout = Workout::factory()->for($user)->create(['name' => 'My workout']);

    $this->actingAs($user);

    Livewire::test(Manage::class)
        ->assertSee('My workout')
        ->assertDontSee('Someone else\'s workout');

    expect($ownWorkout->user_id)->toBe($user->id);
});

test('owner can delete their workout', function () {
    $user = User::factory()->create();
    $workout = Workout::factory()->for($user)->create(['name' => 'Push day']);

    $this->actingAs($user);

    Livewire::test(Manage::class)
        ->call('deleteWorkout', $workout->id)
        ->assertDontSee('Push day');

    expect(Workout::query()->find($workout->id))->toBeNull();
});

test('deleting a workout removes its exercises and sets', function () {
    $user = User::factory()->create();
    $workout = Workout::factory()->for($user)->create();
    $workoutExercise = WorkoutExercise::factory()->for($workout)->create();
    WorkoutExerciseSet::factory()->for($workoutExercise, 'workoutExercise')->create();

    $this->actingAs($user);

    Livewire::test(Manage::class)->call('deleteWorkout', $workout->id);

    expect(WorkoutExercise::query()->where('workout_id', $workout->id)->count())->toBe(0);
});

test('a user cannot delete another user\'s workout', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $workout = Workout::factory()->for($owner)->create();

    $this->actingAs($otherUser);

    Livewire::test(Manage::class)
        ->call('deleteWorkout', $workout->id)
        ->assertForbidden();

    expect(Workout::query()->find($workout->id))->not->toBeNull();
});

test('deleting a workout plan does not delete workouts started from it', function () {
    $user = User::factory()->create();
    $workoutPlan = WorkoutPlan::factory()->for($user)->create();
    $workout = Workout::factory()->for($user)->create(['workout_plan_id' => $workoutPlan->id]);

    $workoutPlan->delete();

    $workout->refresh();

    expect(Workout::query()->find($workout->id))->not->toBeNull();
    expect($workout->workout_plan_id)->toBeNull();
});
