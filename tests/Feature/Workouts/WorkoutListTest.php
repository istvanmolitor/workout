<?php

use App\Livewire\Workouts\Manage;
use App\Models\User;
use App\Models\Workout;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $this->get(route('workouts.index'))->assertRedirect(route('login'));
});

test('workouts page is displayed', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('workouts.index'))->assertOk();
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
