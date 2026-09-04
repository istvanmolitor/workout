<?php

use App\Livewire\Users\Show;
use App\Models\User;
use App\Models\Workout;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $other = User::factory()->create();

    $this->get(route('users.show', $other))->assertRedirect(route('login'));
});

test('a user profile page is displayed', function () {
    $user = User::factory()->create();
    $other = User::factory()->create(['name' => 'Jane Lifter']);

    $this->actingAs($user);

    $this->get(route('users.show', $other))
        ->assertOk()
        ->assertSee('Jane Lifter');
});

test('a user profile shows that user\'s workouts', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    Workout::factory()->for($other)->create(['name' => 'Their workout']);
    Workout::factory()->for($user)->create(['name' => 'My own workout']);

    $this->actingAs($user);

    Livewire::test(Show::class, ['user' => $other])
        ->assertSee('Their workout')
        ->assertDontSee('My own workout');
});

test('the follow button is hidden on your own profile', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(Show::class, ['user' => $user])
        ->assertDontSee(__('Follow'))
        ->assertDontSee(__('Unfollow'));
});

test('a user can follow and unfollow another user from their profile', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(Show::class, ['user' => $other])
        ->assertSee(__('Follow'))
        ->call('follow');

    expect($user->fresh()->isFollowing($other))->toBeTrue();

    Livewire::test(Show::class, ['user' => $other])
        ->assertSee(__('Unfollow'))
        ->call('unfollow');

    expect($user->fresh()->isFollowing($other))->toBeFalse();
});
