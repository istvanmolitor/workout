<?php

use App\Livewire\Users\Search;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $this->get(route('users.index'))->assertRedirect(route('login'));
});

test('users page is displayed', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('users.index'))->assertOk();
});

test('the authenticated user does not see themselves in the list', function () {
    $user = User::factory()->create(['name' => 'Self User']);
    $other = User::factory()->create(['name' => 'Other User']);

    $this->actingAs($user);

    Livewire::test(Search::class)
        ->assertDontSee('Self User')
        ->assertSee('Other User');
});

test('search filters users by name or email', function () {
    $user = User::factory()->create();
    $match = User::factory()->create(['name' => 'Jane Lifter', 'email' => 'jane@example.com']);
    $other = User::factory()->create(['name' => 'Someone Else', 'email' => 'someone@example.com']);

    $this->actingAs($user);

    Livewire::test(Search::class)
        ->set('search', 'jane')
        ->assertSee('Jane Lifter')
        ->assertDontSee('Someone Else');
});

test('a user can follow and unfollow another user', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(Search::class)->call('follow', $other->id);

    expect($user->fresh()->isFollowing($other))->toBeTrue();

    Livewire::test(Search::class)->call('unfollow', $other->id);

    expect($user->fresh()->isFollowing($other))->toBeFalse();
});
