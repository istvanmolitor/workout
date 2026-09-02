<?php

use App\Livewire\Dashboard;
use App\Models\User;
use App\Models\Workout;
use Illuminate\Support\Carbon;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});

test('dashboard calendar shows the current month\'s workouts', function () {
    $user = User::factory()->create();
    $workout = Workout::factory()->for($user)->create([
        'name' => 'Push day',
        'performed_at' => now()->startOfMonth()->addDays(2),
    ]);

    $this->actingAs($user);

    Livewire::test(Dashboard::class)->assertSee('Push day');

    expect($workout->performed_at->isSameMonth(now()))->toBeTrue();
});

test('dashboard calendar only shows the user\'s own workouts', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    Workout::factory()->for($otherUser)->create([
        'name' => 'Someone else\'s workout',
        'performed_at' => now()->startOfMonth()->addDays(2),
    ]);
    Workout::factory()->for($user)->create([
        'name' => 'My workout',
        'performed_at' => now()->startOfMonth()->addDays(2),
    ]);

    $this->actingAs($user);

    Livewire::test(Dashboard::class)
        ->assertSee('My workout')
        ->assertDontSee('Someone else\'s workout');
});

test('dashboard calendar does not show workouts from other months', function () {
    $user = User::factory()->create();
    Workout::factory()->for($user)->create([
        'name' => 'Last month\'s workout',
        'performed_at' => now()->subMonthNoOverflow(),
    ]);

    $this->actingAs($user);

    Livewire::test(Dashboard::class)->assertDontSee('Last month\'s workout');
});

test('user can navigate to the previous and next month', function () {
    $user = User::factory()->create();
    $lastMonthWorkout = Workout::factory()->for($user)->create([
        'name' => 'Last month\'s workout',
        'performed_at' => now()->subMonthNoOverflow()->startOfMonth()->addDays(2),
    ]);

    $this->actingAs($user);

    Livewire::test(Dashboard::class)
        ->assertDontSee('Last month\'s workout')
        ->call('previousMonth')
        ->assertSee('Last month\'s workout')
        ->call('nextMonth')
        ->assertDontSee('Last month\'s workout');

    expect($lastMonthWorkout->performed_at->isSameMonth(now()->subMonthNoOverflow()))->toBeTrue();
});

test('user can jump back to the current month', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test(Dashboard::class)
        ->call('previousMonth')
        ->call('goToToday')
        ->assertSet('year', Carbon::now()->year)
        ->assertSet('month', Carbon::now()->month);
});
