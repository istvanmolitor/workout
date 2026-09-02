<?php

use App\Livewire\WorkoutPlans\Edit;
use App\Models\Exercise;
use App\Models\User;
use App\Models\WorkoutPlan;
use App\Models\WorkoutPlanExercise;
use App\Models\WorkoutPlanExerciseSet;
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
    $oldExercise = WorkoutPlanExercise::factory()->for($workoutPlan)->create(['exercise_id' => Exercise::factory()->create(['name' => 'Old exercise'])]);
    WorkoutPlanExerciseSet::factory()->for($oldExercise, 'workoutPlanExercise')->create(['reps' => 10]);
    $benchPress = Exercise::factory()->create(['name' => 'Bench press']);

    $this->actingAs($user);

    Livewire::test(Edit::class, ['workoutPlan' => $workoutPlan])
        ->set('name', 'Push day (updated)')
        ->set('exercises', [
            ['exercise_id' => $benchPress->id, 'sets' => [['reps' => 12, 'weight' => 60], ['reps' => 10, 'weight' => 65], ['reps' => 8, 'weight' => 70]]],
        ])
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('workout-plans.index'));

    $workoutPlan->refresh();

    expect($workoutPlan->name)->toBe('Push day (updated)');
    expect($workoutPlan->exercises)->toHaveCount(1);
    expect($workoutPlan->exercises->first()->exercise->name)->toBe('Bench press');
    expect($workoutPlan->exercises->first()->sets->pluck('reps')->all())->toBe([12, 10, 8]);
    expect($workoutPlan->exercises->first()->sets->pluck('weight')->map(fn ($weight) => (float) $weight)->all())->toBe([60.0, 65.0, 70.0]);
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
