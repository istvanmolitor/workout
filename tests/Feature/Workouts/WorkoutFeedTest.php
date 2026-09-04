<?php

use App\Livewire\Workouts\Feed;
use App\Models\User;
use App\Models\Workout;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $this->get(route('workouts.feed'))->assertRedirect(route('login'));
});

test('feed only shows workouts from followed users', function () {
    $user = User::factory()->create();
    $followed = User::factory()->create();
    $stranger = User::factory()->create();

    $user->following()->attach($followed->id);

    Workout::factory()->for($followed)->create(['name' => 'Followed workout']);
    Workout::factory()->for($stranger)->create(['name' => 'Stranger workout']);
    Workout::factory()->for($user)->create(['name' => 'My own workout']);

    $this->actingAs($user);

    Livewire::test(Feed::class)
        ->assertSee('Followed workout')
        ->assertDontSee('Stranger workout')
        ->assertDontSee('My own workout');
});

test('feed is empty when the user is not following anyone', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    Workout::factory()->for($other)->create(['name' => 'Someone else\'s workout']);

    $this->actingAs($user);

    Livewire::test(Feed::class)
        ->assertDontSee('Someone else\'s workout')
        ->assertSee(__('Nothing to show yet'));
});
