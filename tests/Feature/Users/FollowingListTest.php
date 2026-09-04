<?php

use App\Livewire\Users\Following;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $this->get(route('users.following'))->assertRedirect(route('login'));
});

test('only followed users appear in the list', function () {
    $user = User::factory()->create();
    $followed = User::factory()->create(['name' => 'Followed User']);
    $notFollowed = User::factory()->create(['name' => 'Stranger']);

    $user->following()->attach($followed->id);

    $this->actingAs($user);

    Livewire::test(Following::class)
        ->assertSee('Followed User')
        ->assertDontSee('Stranger');
});

test('unfollowing removes the user from the list', function () {
    $user = User::factory()->create();
    $followed = User::factory()->create(['name' => 'Followed User']);

    $user->following()->attach($followed->id);

    $this->actingAs($user);

    Livewire::test(Following::class)
        ->call('unfollow', $followed->id)
        ->assertDontSee('Followed User');

    expect($user->fresh()->isFollowing($followed))->toBeFalse();
});
