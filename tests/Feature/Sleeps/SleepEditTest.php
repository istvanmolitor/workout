<?php

use App\Livewire\Sleeps\Edit;
use App\Models\Sleep;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $sleep = Sleep::factory()->create();

    $this->get(route('sleeps.edit', $sleep))->assertRedirect(route('login'));
});

test('owner can view the edit page', function () {
    $user = User::factory()->create();
    $sleep = Sleep::factory()->for($user)->create();

    $this->actingAs($user);

    $this->get(route('sleeps.edit', $sleep))->assertOk();
});

test('a user cannot view another user\'s sleep entry', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $sleep = Sleep::factory()->for($owner)->create();

    $this->actingAs($otherUser);

    $this->get(route('sleeps.edit', $sleep))->assertForbidden();
});

test('owner can update their sleep entry', function () {
    $user = User::factory()->create();
    $sleep = Sleep::factory()->for($user)->create([
        'started_at' => '2026-09-01 23:00:00',
        'ended_at' => '2026-09-02 07:00:00',
    ]);

    $this->actingAs($user);

    Livewire::test(Edit::class, ['sleep' => $sleep])
        ->set('started_at', '2026-09-02T23:30')
        ->set('ended_at', '2026-09-03T06:45')
        ->set('quality', '3')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('sleeps.index'));

    $sleep->refresh();
    expect($sleep->started_at->format('Y-m-d H:i'))->toBe('2026-09-02 23:30');
    expect($sleep->ended_at->format('Y-m-d H:i'))->toBe('2026-09-03 06:45');
    expect($sleep->quality)->toBe(3);
});

test('started_at is required', function () {
    $user = User::factory()->create();
    $sleep = Sleep::factory()->for($user)->create();

    $this->actingAs($user);

    Livewire::test(Edit::class, ['sleep' => $sleep])
        ->set('started_at', '')
        ->call('save')
        ->assertHasErrors(['started_at' => 'required']);
});

test('started_at must stay unique per user', function () {
    $user = User::factory()->create();
    Sleep::factory()->for($user)->create(['started_at' => '2026-09-01 23:00:00']);
    $sleep = Sleep::factory()->for($user)->create(['started_at' => '2026-09-02 23:00:00']);

    $this->actingAs($user);

    Livewire::test(Edit::class, ['sleep' => $sleep])
        ->set('started_at', '2026-09-01T23:00')
        ->call('save')
        ->assertHasErrors(['started_at' => 'unique']);
});
