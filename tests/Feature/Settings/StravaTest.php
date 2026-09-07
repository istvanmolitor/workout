<?php

use App\Livewire\Settings\Strava;
use App\Models\User;
use Laravel\Socialite\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Livewire\Livewire;

test('strava settings page shows a connect button when not connected', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('strava.edit'))
        ->assertOk()
        ->assertSee(__('Your account is not connected'))
        ->assertSeeHtml(route('strava.redirect'));
});

test('strava settings page shows a disconnect button when connected', function () {
    $user = User::factory()->create(['strava_id' => '123']);

    $this->actingAs($user)
        ->get(route('strava.edit'))
        ->assertOk()
        ->assertSee(__('Your account is connected'));
});

test('the redirect route sends the user to strava', function () {
    Socialite::fake('strava');

    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('strava.redirect'));

    $response->assertRedirect();
});

test('the callback connects the strava account to the authenticated user', function () {
    Socialite::fake('strava', SocialiteUser::fake([
        'id' => 'strava-123',
        'token' => 'access-token',
        'refreshToken' => 'refresh-token',
        'expiresIn' => 21600,
    ]));

    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('strava.callback'));

    $response->assertRedirect(route('strava.edit'));

    expect($user->refresh())
        ->strava_id->toBe('strava-123')
        ->strava_token->toBe('access-token')
        ->strava_refresh_token->toBe('refresh-token')
        ->strava_token_expires_at->not->toBeNull();
});

test('disconnecting removes the strava tokens from the user', function () {
    $user = User::factory()->create([
        'strava_id' => '123',
        'strava_token' => 'token',
        'strava_refresh_token' => 'refresh',
        'strava_token_expires_at' => now()->addHour(),
    ]);

    $this->actingAs($user);

    Livewire::test(Strava::class)->call('disconnect');

    expect($user->refresh())
        ->strava_id->toBeNull()
        ->strava_token->toBeNull()
        ->strava_refresh_token->toBeNull()
        ->strava_token_expires_at->toBeNull();
});
