<?php

use App\Livewire\Meals\Manage;
use App\Models\Food;
use App\Models\Meal;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $this->get(route('meals.index'))->assertRedirect(route('login'));
});

test('meal list page is displayed', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('meals.index'))->assertOk();
});

test('meal list page links back to the dashboard', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(Manage::class)->assertSeeHtml(route('dashboard'));
});

test('admins see a link to the foods catalog', function () {
    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(Manage::class)->assertSeeHtml(route('foods.index'));
});

test('non-admins do not see a link to the foods catalog', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(Manage::class)->assertDontSeeHtml(route('foods.index'));
});

test('meal list shows all foods eaten in a meal', function () {
    $user = User::factory()->create();
    $meal = Meal::factory()->for($user)->create();
    $meal->foods()->sync([
        Food::factory()->create(['name' => 'Csirkemell'])->id,
        Food::factory()->create(['name' => 'Rizs'])->id,
    ]);

    $this->actingAs($user);

    Livewire::test(Manage::class)
        ->assertSee('Csirkemell')
        ->assertSee('Rizs');
});

test('user only sees their own meal entries', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $otherMeal = Meal::factory()->for($otherUser)->create();
    $otherMeal->foods()->sync([Food::factory()->create(['name' => 'Idegen étel'])->id]);

    $ownMeal = Meal::factory()->for($user)->create();
    $ownMeal->foods()->sync([Food::factory()->create(['name' => 'Saját étel'])->id]);

    $this->actingAs($user);

    Livewire::test(Manage::class)
        ->assertSee('Saját étel')
        ->assertDontSee('Idegen étel');
});

test('user can delete their own meal entry', function () {
    $user = User::factory()->create();
    $meal = Meal::factory()->for($user)->create();

    $this->actingAs($user);

    Livewire::test(Manage::class)
        ->call('delete', $meal);

    expect(Meal::query()->find($meal->id))->toBeNull();
});

test('user cannot delete another user\'s meal entry', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $meal = Meal::factory()->for($owner)->create();

    $this->actingAs($otherUser);

    Livewire::test(Manage::class)
        ->call('delete', $meal)
        ->assertForbidden();

    expect(Meal::query()->find($meal->id))->not->toBeNull();
});
