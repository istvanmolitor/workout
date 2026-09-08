<?php

use App\Livewire\Meals\Edit;
use App\Models\Food;
use App\Models\Meal;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $meal = Meal::factory()->create();

    $this->get(route('meals.edit', $meal))->assertRedirect(route('login'));
});

test('owner can view the edit page', function () {
    $user = User::factory()->create();
    $meal = Meal::factory()->for($user)->create();

    $this->actingAs($user);

    $this->get(route('meals.edit', $meal))->assertOk();
});

test('a user cannot view another user\'s meal entry', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $meal = Meal::factory()->for($owner)->create();

    $this->actingAs($otherUser);

    $this->get(route('meals.edit', $meal))->assertForbidden();
});

test('editing a meal preselects its current foods', function () {
    $user = User::factory()->create();
    $meal = Meal::factory()->for($user)->create();
    $existingFood = $meal->foods->first();

    $this->actingAs($user);

    Livewire::test(Edit::class, ['meal' => $meal])
        ->assertSet('foodIds', [$existingFood->id]);
});

test('owner can update their meal entry and its foods', function () {
    $user = User::factory()->create();
    $meal = Meal::factory()->for($user)->create([
        'eaten_at' => '2026-09-01 08:00:00',
    ]);
    $originalFood = $meal->foods->first();
    $newFood = Food::factory()->create(['name' => 'Rántotta']);

    $this->actingAs($user);

    Livewire::test(Edit::class, ['meal' => $meal])
        ->call('removeFood', $originalFood->id)
        ->call('addFood', $newFood)
        ->set('foodQuantities.'.$newFood->id, '80')
        ->set('eaten_at', '2026-09-01T08:30')
        ->set('type', 'breakfast')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('meals.index'));

    $meal->refresh();
    expect($meal->foods->pluck('id')->all())->toBe([$newFood->id]);
    expect($meal->eaten_at->format('Y-m-d H:i'))->toBe('2026-09-01 08:30');
    expect($meal->type)->toBe('breakfast');
});

test('at least one food is required', function () {
    $user = User::factory()->create();
    $meal = Meal::factory()->for($user)->create();
    $existingFood = $meal->foods->first();

    $this->actingAs($user);

    Livewire::test(Edit::class, ['meal' => $meal])
        ->call('removeFood', $existingFood->id)
        ->call('save')
        ->assertHasErrors(['foodIds' => 'required']);
});

test('a quantity is required for a newly added food', function () {
    $user = User::factory()->create();
    $meal = Meal::factory()->for($user)->create();
    $newFood = Food::factory()->create();

    $this->actingAs($user);

    Livewire::test(Edit::class, ['meal' => $meal])
        ->call('addFood', $newFood)
        ->call('save')
        ->assertHasErrors(['foodQuantities.'.$newFood->id => 'required']);
});

test('editing a meal preselects the recorded quantity for each food', function () {
    $user = User::factory()->create();
    $meal = Meal::factory()->for($user)->create();
    $existingFood = $meal->foods()->first();
    $meal->foods()->updateExistingPivot($existingFood->id, ['quantity' => 120]);

    $this->actingAs($user);

    Livewire::test(Edit::class, ['meal' => $meal])
        ->assertSet('foodQuantities.'.$existingFood->id, '120.00');
});

test('owner can update the quantity eaten for a food', function () {
    $user = User::factory()->create();
    $meal = Meal::factory()->for($user)->create();
    $existingFood = $meal->foods->first();

    $this->actingAs($user);

    Livewire::test(Edit::class, ['meal' => $meal])
        ->set('foodQuantities.'.$existingFood->id, '200')
        ->call('save')
        ->assertHasNoErrors();

    expect((float) $meal->foods()->first()->pivot->quantity)->toBe(200.0);
});
