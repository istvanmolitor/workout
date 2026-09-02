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

test('authenticated user can create a workout plan with exercises', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(Create::class)
        ->set('name', 'Push day')
        ->set('description', 'Chest, shoulders and triceps')
        ->set('exercises', [
            ['name' => 'Bench press', 'sets' => 4, 'reps' => 8],
            ['name' => 'Shoulder press', 'sets' => 3, 'reps' => 10],
        ])
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('workout-plans.index'));

    $workoutPlan = WorkoutPlan::query()->where('user_id', $user->id)->sole();

    expect($workoutPlan->name)->toBe('Push day');
    expect($workoutPlan->description)->toBe('Chest, shoulders and triceps');
    expect($workoutPlan->exercises)->toHaveCount(2);
    expect($workoutPlan->exercises->first()->name)->toBe('Bench press');
});

test('workout plan name is required', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(Create::class)
        ->set('name', '')
        ->set('exercises', [['name' => 'Bench press', 'sets' => 4, 'reps' => 8]])
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

test('exercise name is required', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(Create::class)
        ->set('name', 'Push day')
        ->set('exercises', [['name' => '', 'sets' => 4, 'reps' => 8]])
        ->call('save')
        ->assertHasErrors(['exercises.0.name' => 'required']);
});
