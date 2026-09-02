<?php

use App\Livewire\WorkoutPlans\Manage;
use App\Models\User;
use App\Models\WorkoutPlan;
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
