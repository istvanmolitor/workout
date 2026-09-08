<?php

use App\Livewire\Nutrients\Manage;
use App\Models\Food;
use App\Models\Nutrient;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $this->get(route('nutrients.index'))->assertRedirect(route('login'));
});

test('non-admins cannot view the nutrients page', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('nutrients.index'))->assertForbidden();
});

test('nutrients page is displayed', function () {
    $this->actingAs(User::factory()->admin()->create());

    $this->get(route('nutrients.index'))->assertOk();
});

test('lists nutrients', function () {
    $this->actingAs(User::factory()->admin()->create());

    Nutrient::factory()->create(['name' => 'Fehérje']);
    Nutrient::factory()->create(['name' => 'Zsír']);

    Livewire::test(Manage::class)
        ->assertSee('Fehérje')
        ->assertSee('Zsír');
});

test('an unused nutrient can be deleted', function () {
    $this->actingAs(User::factory()->admin()->create());

    $nutrient = Nutrient::factory()->create();

    Livewire::test(Manage::class)
        ->call('delete', $nutrient->id);

    expect(Nutrient::query()->find($nutrient->id))->toBeNull();
});

test('a nutrient with recorded values cannot be deleted', function () {
    $this->actingAs(User::factory()->admin()->create());

    $nutrient = Nutrient::factory()->create();
    $food = Food::factory()->create();
    $food->nutrients()->attach($nutrient->id, ['value' => 12.5]);

    Livewire::test(Manage::class)
        ->call('delete', $nutrient->id);

    expect(Nutrient::query()->find($nutrient->id))->not->toBeNull();
});
