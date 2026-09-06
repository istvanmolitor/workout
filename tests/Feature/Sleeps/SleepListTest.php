<?php

use App\Livewire\Sleeps\Manage;
use App\Models\Sleep;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $this->get(route('sleeps.index'))->assertRedirect(route('login'));
});

test('sleep list page is displayed', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('sleeps.index'))->assertOk();
});

test('user only sees their own sleep entries', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    Sleep::factory()->for($otherUser)->create(['started_at' => '2026-09-01 21:15:00', 'ended_at' => '2026-09-02 05:15:00']);
    Sleep::factory()->for($user)->create(['started_at' => '2026-09-02 23:10:00', 'ended_at' => '2026-09-03 06:40:00']);

    $this->actingAs($user);

    Livewire::test(Manage::class)
        ->assertSee('23:10')
        ->assertDontSee('21:15');
});

test('user can delete their own sleep entry', function () {
    $user = User::factory()->create();
    $sleep = Sleep::factory()->for($user)->create();

    $this->actingAs($user);

    Livewire::test(Manage::class)
        ->call('delete', $sleep);

    expect(Sleep::query()->find($sleep->id))->toBeNull();
});

test('user cannot delete another user\'s sleep entry', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $sleep = Sleep::factory()->for($owner)->create();

    $this->actingAs($otherUser);

    Livewire::test(Manage::class)
        ->call('delete', $sleep)
        ->assertForbidden();

    expect(Sleep::query()->find($sleep->id))->not->toBeNull();
});
