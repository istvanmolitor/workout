<?php

use App\Livewire\BodyWeights\Edit;
use App\Models\BodyWeight;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $bodyWeight = BodyWeight::factory()->create();

    $this->get(route('body-weights.edit', $bodyWeight))->assertRedirect(route('login'));
});

test('owner can view the edit page', function () {
    $user = User::factory()->create();
    $bodyWeight = BodyWeight::factory()->for($user)->create();

    $this->actingAs($user);

    $this->get(route('body-weights.edit', $bodyWeight))->assertOk();
});

test('a user cannot view another user\'s body weight entry', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $bodyWeight = BodyWeight::factory()->for($owner)->create();

    $this->actingAs($otherUser);

    $this->get(route('body-weights.edit', $bodyWeight))->assertForbidden();
});

test('owner can update their body weight entry', function () {
    $user = User::factory()->create();
    $bodyWeight = BodyWeight::factory()->for($user)->create(['weight' => 80, 'measured_at' => '2026-09-01']);

    $this->actingAs($user);

    Livewire::test(Edit::class, ['bodyWeight' => $bodyWeight])
        ->set('weight', '79.4')
        ->set('measured_at', '2026-09-02')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('body-weights.index'));

    $bodyWeight->refresh();
    expect((float) $bodyWeight->weight)->toBe(79.4);
    expect($bodyWeight->measured_at->toDateString())->toBe('2026-09-02');
});

test('weight is required', function () {
    $user = User::factory()->create();
    $bodyWeight = BodyWeight::factory()->for($user)->create();

    $this->actingAs($user);

    Livewire::test(Edit::class, ['bodyWeight' => $bodyWeight])
        ->set('weight', '')
        ->call('save')
        ->assertHasErrors(['weight' => 'required']);
});

test('measured_at must stay unique per user', function () {
    $user = User::factory()->create();
    BodyWeight::factory()->for($user)->create(['measured_at' => '2026-09-01']);
    $bodyWeight = BodyWeight::factory()->for($user)->create(['measured_at' => '2026-09-02']);

    $this->actingAs($user);

    Livewire::test(Edit::class, ['bodyWeight' => $bodyWeight])
        ->set('measured_at', '2026-09-01')
        ->call('save')
        ->assertHasErrors(['measured_at' => 'unique']);
});
