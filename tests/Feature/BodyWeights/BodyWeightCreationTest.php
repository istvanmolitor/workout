<?php

use App\Livewire\BodyWeights\Create;
use App\Models\BodyWeight;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $this->get(route('body-weights.create'))->assertRedirect(route('login'));
});

test('create body weight page is displayed', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('body-weights.create'))->assertOk();
});

test('authenticated user can log a body weight entry', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test(Create::class)
        ->set('weight', '82.5')
        ->set('measured_at', '2026-09-01')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('body-weights.index'));

    $bodyWeight = BodyWeight::query()->where('user_id', $user->id)->first();
    expect($bodyWeight)->not->toBeNull();
    expect((float) $bodyWeight->weight)->toBe(82.5);
    expect($bodyWeight->measured_at->toDateString())->toBe('2026-09-01');
});

test('weight is required', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(Create::class)
        ->set('weight', '')
        ->set('measured_at', '2026-09-01')
        ->call('save')
        ->assertHasErrors(['weight' => 'required']);
});

test('measured_at cannot be in the future', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(Create::class)
        ->set('weight', '80')
        ->set('measured_at', now()->addDay()->toDateString())
        ->call('save')
        ->assertHasErrors(['measured_at' => 'before_or_equal']);
});

test('user cannot log two entries for the same day', function () {
    $user = User::factory()->create();
    BodyWeight::factory()->for($user)->create(['measured_at' => '2026-09-01']);

    $this->actingAs($user);

    Livewire::test(Create::class)
        ->set('weight', '80')
        ->set('measured_at', '2026-09-01')
        ->call('save')
        ->assertHasErrors(['measured_at' => 'unique']);
});
