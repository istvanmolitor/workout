<?php

use App\Livewire\Dashboard;
use App\Models\BodyWeight;
use App\Models\User;
use App\Models\Workout;
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

test('dashboard shows the user\'s most recently logged workout', function () {
    $user = User::factory()->create();
    Workout::factory()->for($user)->create([
        'name' => 'Older workout',
        'performed_at' => now()->subDays(5),
    ]);
    Workout::factory()->for($user)->create([
        'name' => 'Latest workout',
        'performed_at' => now()->subDay(),
    ]);

    $this->actingAs($user);

    Livewire::test(Dashboard::class)
        ->assertSee('Latest workout')
        ->assertDontSee('Older workout')
        ->assertSeeHtml(route('workout-plans.index'));
});

test('dashboard only shows the user\'s own last workout', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    Workout::factory()->for($otherUser)->create(['name' => 'Someone else\'s workout']);

    $this->actingAs($user);

    Livewire::test(Dashboard::class)
        ->assertSee(__('No workouts yet'))
        ->assertDontSee('Someone else\'s workout');
});

test('dashboard prompts to start a workout when none are logged yet', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test(Dashboard::class)
        ->assertSee(__('No workouts yet'))
        ->assertSeeHtml(route('workout-plans.index'));
});

test('dashboard shows a weight chart when the user has multiple body weight entries', function () {
    $user = User::factory()->create();
    BodyWeight::factory()->for($user)->create(['weight' => 80, 'measured_at' => now()->subDays(2)]);
    BodyWeight::factory()->for($user)->create(['weight' => 79, 'measured_at' => now()->subDay()]);

    $this->actingAs($user);

    Livewire::test(Dashboard::class)
        ->assertSee('79.00 kg')
        ->assertSeeHtml('<polyline');

    expect(Livewire::test(Dashboard::class)->get('bodyWeightChartPoints'))->toHaveCount(2);
});

test('dashboard weight chart only reflects the user\'s own body weight entries', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    BodyWeight::factory()->for($otherUser)->create(['weight' => 91.5]);

    $this->actingAs($user);

    Livewire::test(Dashboard::class)->assertDontSee('91.50');
});

test('dashboard prompts for a first body weight entry when none are logged', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test(Dashboard::class)->assertSee(__('No body weight entries yet'));
});
