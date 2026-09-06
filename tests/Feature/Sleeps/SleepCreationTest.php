<?php

use App\Livewire\Sleeps\Create;
use App\Models\Sleep;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $this->get(route('sleeps.create'))->assertRedirect(route('login'));
});

test('create sleep page is displayed', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('sleeps.create'))->assertOk();
});

test('authenticated user can log a sleep entry', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test(Create::class)
        ->set('started_at', '2026-09-01T23:00')
        ->set('ended_at', '2026-09-02T07:00')
        ->set('quality', '4')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('sleeps.index'));

    $sleep = Sleep::query()->where('user_id', $user->id)->first();
    expect($sleep)->not->toBeNull();
    expect($sleep->started_at->format('Y-m-d H:i'))->toBe('2026-09-01 23:00');
    expect($sleep->ended_at->format('Y-m-d H:i'))->toBe('2026-09-02 07:00');
    expect($sleep->quality)->toBe(4);
});

test('started_at is required', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(Create::class)
        ->set('started_at', '')
        ->set('ended_at', '2026-09-02T07:00')
        ->call('save')
        ->assertHasErrors(['started_at' => 'required']);
});

test('ended_at must be after started_at', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(Create::class)
        ->set('started_at', '2026-09-01T23:00')
        ->set('ended_at', '2026-09-01T22:00')
        ->call('save')
        ->assertHasErrors(['ended_at' => 'after']);
});

test('user cannot log two entries with the same started_at', function () {
    $user = User::factory()->create();
    Sleep::factory()->for($user)->create(['started_at' => '2026-09-01 23:00:00']);

    $this->actingAs($user);

    Livewire::test(Create::class)
        ->set('started_at', '2026-09-01T23:00')
        ->set('ended_at', '2026-09-02T07:00')
        ->call('save')
        ->assertHasErrors(['started_at' => 'unique']);
});
