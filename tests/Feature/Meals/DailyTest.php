<?php

use App\Livewire\Meals\Daily;
use App\Models\Food;
use App\Models\Meal;
use App\Models\Nutrient;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $this->get(route('meals.daily'))->assertRedirect(route('login'));
});

test('daily meals page is displayed', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('meals.daily'))->assertOk();
});

test('an invalid date aborts with a 404', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('meals.daily', ['date' => 'not-a-date']))->assertNotFound();
});

test('it only shows meals eaten on the given day', function () {
    $user = User::factory()->create();

    $today = Meal::factory()->for($user)->create(['eaten_at' => '2026-09-08 08:00:00']);
    $today->foods()->sync([Food::factory()->create(['name' => 'Mai étel'])->id]);

    $yesterday = Meal::factory()->for($user)->create(['eaten_at' => '2026-09-07 08:00:00']);
    $yesterday->foods()->sync([Food::factory()->create(['name' => 'Tegnapi étel'])->id]);

    $this->actingAs($user);

    Livewire::test(Daily::class, ['date' => '2026-09-08'])
        ->assertSee('Mai étel')
        ->assertDontSee('Tegnapi étel');
});

test('it only shows the authenticated user\'s meals', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $ownMeal = Meal::factory()->for($user)->create(['eaten_at' => '2026-09-08 08:00:00']);
    $ownMeal->foods()->sync([Food::factory()->create(['name' => 'Saját étel'])->id]);

    $otherMeal = Meal::factory()->for($otherUser)->create(['eaten_at' => '2026-09-08 08:00:00']);
    $otherMeal->foods()->sync([Food::factory()->create(['name' => 'Idegen étel'])->id]);

    $this->actingAs($user);

    Livewire::test(Daily::class, ['date' => '2026-09-08'])
        ->assertSee('Saját étel')
        ->assertDontSee('Idegen étel');
});

test('it groups meals by type', function () {
    $user = User::factory()->create();

    $breakfast = Meal::factory()->for($user)->create(['eaten_at' => '2026-09-08 08:00:00', 'type' => 'breakfast']);
    $breakfast->foods()->sync([Food::factory()->create(['name' => 'Zabkása'])->id]);

    $dinner = Meal::factory()->for($user)->create(['eaten_at' => '2026-09-08 19:00:00', 'type' => 'dinner']);
    $dinner->foods()->sync([Food::factory()->create(['name' => 'Csirkemell'])->id]);

    $this->actingAs($user);

    Livewire::test(Daily::class, ['date' => '2026-09-08'])
        ->assertSeeInOrder(['Reggeli', 'Zabkása', 'Vacsora', 'Csirkemell']);
});

test('it totals calories scaled by the quantity eaten', function () {
    $user = User::factory()->create();

    $meal = Meal::factory()->for($user)->create(['eaten_at' => '2026-09-08 08:00:00']);
    $food = Food::factory()->create(['calories' => 200]);
    $meal->foods()->sync([$food->id => ['quantity' => 150]]);

    $this->actingAs($user);

    Livewire::test(Daily::class, ['date' => '2026-09-08'])
        ->assertSee('300')
        ->assertSee('300 kcal');
});

test('it totals nutrient values scaled by the quantity eaten', function () {
    $user = User::factory()->create();
    $nutrient = Nutrient::factory()->create(['name' => 'Fehérje', 'unit' => 'g', 'decimal_places' => 1]);

    $meal = Meal::factory()->for($user)->create(['eaten_at' => '2026-09-08 08:00:00']);
    $food = Food::factory()->create(['calories' => null]);
    $food->nutrients()->attach($nutrient->id, ['value' => 20]);
    $meal->foods()->sync([$food->id => ['quantity' => 250]]);

    $this->actingAs($user);

    Livewire::test(Daily::class, ['date' => '2026-09-08'])
        ->assertSee('Fehérje')
        ->assertSee('50.0 g');
});

test('user can navigate to the previous and next day', function () {
    $this->actingAs(User::factory()->create());

    Livewire::withoutLazyLoading()
        ->test(Daily::class, ['date' => '2026-09-08'])
        ->call('previousDay')
        ->assertRedirect(route('meals.daily', ['date' => '2026-09-07']));

    Livewire::withoutLazyLoading()
        ->test(Daily::class, ['date' => '2026-09-08'])
        ->call('nextDay')
        ->assertRedirect(route('meals.daily', ['date' => '2026-09-09']));
});
