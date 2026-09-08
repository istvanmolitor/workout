<?php

use App\Livewire\Meals\Create;
use App\Models\Food;
use App\Models\Meal;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $this->get(route('meals.create'))->assertRedirect(route('login'));
});

test('create meal page is displayed', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('meals.create'))->assertOk();
});

test('typing a search term shows matching foods', function () {
    $this->actingAs(User::factory()->create());
    Food::factory()->create(['name' => 'Csirkemell rizzsel']);
    Food::factory()->create(['name' => 'Túró rudi']);

    Livewire::test(Create::class)
        ->set('foodSearch', 'csirke')
        ->assertSee('Csirkemell rizzsel')
        ->assertDontSee('Túró rudi');
});

test('adding a food from the results attaches it to the list of selected foods', function () {
    $this->actingAs(User::factory()->create());
    $food = Food::factory()->create(['name' => 'Csirkemell rizzsel']);

    Livewire::test(Create::class)
        ->set('foodSearch', 'csirke')
        ->call('addFood', $food)
        ->assertSet('foodIds', [$food->id])
        ->assertSet('foodSearch', '')
        ->assertSee('Csirkemell rizzsel');
});

test('multiple foods can be added to a meal', function () {
    $this->actingAs(User::factory()->create());
    $first = Food::factory()->create();
    $second = Food::factory()->create();

    Livewire::test(Create::class)
        ->call('addFood', $first)
        ->call('addFood', $second)
        ->assertSet('foodIds', [$first->id, $second->id]);
});

test('a food can be removed from the list', function () {
    $this->actingAs(User::factory()->create());
    $first = Food::factory()->create();
    $second = Food::factory()->create();

    Livewire::test(Create::class)
        ->call('addFood', $first)
        ->call('addFood', $second)
        ->call('removeFood', $first->id)
        ->assertSet('foodIds', [$second->id]);
});

test('creating a new food from the search term adds it to the catalog and attaches it', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(Create::class)
        ->set('foodSearch', 'Zabkása')
        ->set('newFoodCalories', '350')
        ->call('createFood')
        ->assertSee('Zabkása');

    $food = Food::query()->where('name', 'Zabkása')->first();
    expect($food)->not->toBeNull();
    expect($food->calories)->toBe(350);
});

test('creating a food that already exists reuses it instead of duplicating', function () {
    $this->actingAs(User::factory()->create());
    $existing = Food::factory()->create(['name' => 'Zabkása']);

    Livewire::test(Create::class)
        ->set('foodSearch', 'zabkása')
        ->call('createFood')
        ->assertSet('foodIds', [$existing->id]);

    expect(Food::query()->count())->toBe(1);
});

test('authenticated user can log a meal entry with multiple foods', function () {
    $user = User::factory()->create();
    $first = Food::factory()->create();
    $second = Food::factory()->create();
    $this->actingAs($user);

    Livewire::test(Create::class)
        ->call('addFood', $first)
        ->call('addFood', $second)
        ->set('foodQuantities.'.$first->id, '100')
        ->set('foodQuantities.'.$second->id, '50')
        ->set('eaten_at', '2026-09-01T12:30')
        ->set('type', 'lunch')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('meals.index'));

    $meal = Meal::query()->where('user_id', $user->id)->first();
    expect($meal)->not->toBeNull();
    expect($meal->foods->pluck('id')->sort()->values()->all())->toBe(collect([$first->id, $second->id])->sort()->values()->all());
    expect($meal->eaten_at->format('Y-m-d H:i'))->toBe('2026-09-01 12:30');
    expect($meal->type)->toBe('lunch');
});

test('at least one food is required', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(Create::class)
        ->set('eaten_at', '2026-09-01T12:30')
        ->call('save')
        ->assertHasErrors(['foodIds' => 'required']);
});

test('a quantity eaten can be recorded for a food', function () {
    $user = User::factory()->create();
    $food = Food::factory()->create();
    $this->actingAs($user);

    Livewire::test(Create::class)
        ->call('addFood', $food)
        ->set('foodQuantities.'.$food->id, '150.5')
        ->set('eaten_at', '2026-09-01T12:30')
        ->call('save')
        ->assertHasNoErrors();

    $meal = Meal::query()->where('user_id', $user->id)->first();
    expect((float) $meal->foods->find($food->id)->pivot->quantity)->toBe(150.5);
});

test('a quantity is required for each added food', function () {
    $user = User::factory()->create();
    $food = Food::factory()->create();
    $this->actingAs($user);

    Livewire::test(Create::class)
        ->call('addFood', $food)
        ->set('eaten_at', '2026-09-01T12:30')
        ->call('save')
        ->assertHasErrors(['foodQuantities.'.$food->id => 'required']);
});

test('quantity must be a valid number', function () {
    $user = User::factory()->create();
    $food = Food::factory()->create();
    $this->actingAs($user);

    Livewire::test(Create::class)
        ->call('addFood', $food)
        ->set('foodQuantities.'.$food->id, 'lots')
        ->set('eaten_at', '2026-09-01T12:30')
        ->call('save')
        ->assertHasErrors(['foodQuantities.'.$food->id => 'numeric']);
});

test('eaten_at is required', function () {
    $this->actingAs(User::factory()->create());
    $food = Food::factory()->create();

    Livewire::test(Create::class)
        ->call('addFood', $food)
        ->set('eaten_at', '')
        ->call('save')
        ->assertHasErrors(['eaten_at' => 'required']);
});

test('type must be a valid option', function () {
    $this->actingAs(User::factory()->create());
    $food = Food::factory()->create();

    Livewire::test(Create::class)
        ->call('addFood', $food)
        ->set('eaten_at', '2026-09-01T08:00')
        ->set('type', 'brunch')
        ->call('save')
        ->assertHasErrors(['type' => 'in']);
});
