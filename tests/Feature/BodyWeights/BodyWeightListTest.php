<?php

use App\Livewire\BodyWeights\Manage;
use App\Models\BodyWeight;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $this->get(route('body-weights.index'))->assertRedirect(route('login'));
});

test('body weight list page is displayed', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('body-weights.index'))->assertOk();
});

test('user only sees their own body weight entries', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    BodyWeight::factory()->for($otherUser)->create(['weight' => 91.5]);
    BodyWeight::factory()->for($user)->create(['weight' => 82.3]);

    $this->actingAs($user);

    Livewire::test(Manage::class)
        ->assertSee('82.30')
        ->assertDontSee('91.50');
});

test('user can delete their own body weight entry', function () {
    $user = User::factory()->create();
    $bodyWeight = BodyWeight::factory()->for($user)->create();

    $this->actingAs($user);

    Livewire::test(Manage::class)
        ->call('delete', $bodyWeight);

    expect(BodyWeight::query()->find($bodyWeight->id))->toBeNull();
});

test('user cannot delete another user\'s body weight entry', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $bodyWeight = BodyWeight::factory()->for($owner)->create();

    $this->actingAs($otherUser);

    Livewire::test(Manage::class)
        ->call('delete', $bodyWeight)
        ->assertForbidden();

    expect(BodyWeight::query()->find($bodyWeight->id))->not->toBeNull();
});
