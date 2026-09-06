<?php

use App\Livewire\Calendar\Show;
use App\Models\BodyWeight;
use App\Models\Sleep;
use App\Models\User;
use App\Models\Workout;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('calendar.show', '2026-09-06'));
    $response->assertRedirect(route('login'));
});

test('an invalid date returns a 404', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('calendar.show', 'not-a-date'))->assertNotFound();
    $this->get(route('calendar.show', '2026-02-30'))->assertNotFound();
});

test('the day view shows the workouts, body weight, and sleep for that day', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $workout = Workout::factory()->for($user)->create([
        'name' => 'Push day',
        'performed_at' => '2026-09-06',
    ]);
    $bodyWeight = BodyWeight::factory()->for($user)->create([
        'weight' => 82.50,
        'measured_at' => '2026-09-06',
    ]);
    $sleep = Sleep::factory()->for($user)->create([
        'started_at' => '2026-09-05 23:00:00',
        'ended_at' => '2026-09-06 07:00:00',
    ]);

    Livewire::test(Show::class, ['date' => '2026-09-06'])
        ->assertSee('Push day')
        ->assertSee('82.50 kg')
        ->assertSee('8 ó 0 p');

    expect($workout->performed_at->toDateString())->toBe('2026-09-06')
        ->and($bodyWeight->measured_at->toDateString())->toBe('2026-09-06')
        ->and($sleep->ended_at->toDateString())->toBe('2026-09-06');
});

test('the day view only shows the user\'s own data', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    Workout::factory()->for($otherUser)->create([
        'name' => 'Someone else\'s workout',
        'performed_at' => '2026-09-06',
    ]);

    $this->actingAs($user);

    Livewire::test(Show::class, ['date' => '2026-09-06'])
        ->assertDontSee('Someone else\'s workout')
        ->assertSee(__('No workouts logged on this day'));
});

test('the day view does not show data from other days', function () {
    $user = User::factory()->create();
    Workout::factory()->for($user)->create([
        'name' => 'Yesterday\'s workout',
        'performed_at' => '2026-09-05',
    ]);

    $this->actingAs($user);

    Livewire::test(Show::class, ['date' => '2026-09-06'])
        ->assertDontSee('Yesterday\'s workout');
});

test('user can navigate to the previous and next day', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test(Show::class, ['date' => '2026-09-06'])
        ->call('previousDay')
        ->assertRedirect(route('calendar.show', '2026-09-05'));

    Livewire::test(Show::class, ['date' => '2026-09-06'])
        ->call('nextDay')
        ->assertRedirect(route('calendar.show', '2026-09-07'));
});
