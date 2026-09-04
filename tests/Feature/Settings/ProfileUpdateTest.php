<?php

use App\Livewire\Settings\Profile;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('profile page is displayed', function () {
    $this->actingAs($user = User::factory()->create());

    $this->get('/settings/profile')->assertOk();
});

test('profile information can be updated', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = Livewire::test(Profile::class)
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->call('updateProfileInformation');

    $response->assertHasNoErrors();

    $user->refresh();

    expect($user->name)->toEqual('Test User');
    expect($user->email)->toEqual('test@example.com');
    expect($user->email_verified_at)->toBeNull();
});

test('email verification status is unchanged when email address is unchanged', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = Livewire::test(Profile::class)
        ->set('name', 'Test User')
        ->set('email', $user->email)
        ->call('updateProfileInformation');

    $response->assertHasNoErrors();

    expect($user->refresh()->email_verified_at)->not->toBeNull();
});

test('user can delete their account', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = Livewire::test('settings.delete-user-form')
        ->set('password', 'password')
        ->call('deleteUser');

    $response
        ->assertHasNoErrors()
        ->assertRedirect('/');

    expect($user->fresh())->toBeNull();
    expect(auth()->check())->toBeFalse();
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = Livewire::test('settings.delete-user-form')
        ->set('password', 'wrong-password')
        ->call('deleteUser');

    $response->assertHasErrors(['password']);

    expect($user->fresh())->not->toBeNull();
});

test('user can upload a profile picture', function () {
    Storage::fake('public');

    $user = User::factory()->create();

    $this->actingAs($user);

    $response = Livewire::test(Profile::class)
        ->set('avatar', UploadedFile::fake()->create('avatar.jpg', 10, 'image/jpeg'))
        ->call('updateAvatar');

    $response->assertHasNoErrors();

    $user->refresh();

    expect($user->avatar)->not->toBeNull();
    Storage::disk('public')->assertExists($user->avatar);
});

test('uploading a new profile picture replaces the old one', function () {
    Storage::fake('public');

    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(Profile::class)
        ->set('avatar', UploadedFile::fake()->create('first.jpg', 10, 'image/jpeg'))
        ->call('updateAvatar');

    $firstAvatar = $user->refresh()->avatar;

    Livewire::test(Profile::class)
        ->set('avatar', UploadedFile::fake()->create('second.jpg', 10, 'image/jpeg'))
        ->call('updateAvatar');

    $user->refresh();

    Storage::disk('public')->assertMissing($firstAvatar);
    Storage::disk('public')->assertExists($user->avatar);
});

test('user can remove their profile picture', function () {
    Storage::fake('public');

    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(Profile::class)
        ->set('avatar', UploadedFile::fake()->create('avatar.jpg', 10, 'image/jpeg'))
        ->call('updateAvatar');

    $avatar = $user->refresh()->avatar;

    Livewire::test(Profile::class)->call('removeAvatar');

    Storage::disk('public')->assertMissing($avatar);
    expect($user->refresh()->avatar)->toBeNull();
});
