<?php

use App\Livewire\Foods\Edit;
use App\Models\Food;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $food = Food::factory()->create();

    $this->get(route('foods.edit', $food))->assertRedirect(route('login'));
});

test('non-admins cannot view the edit food page', function () {
    $this->actingAs(User::factory()->create());
    $food = Food::factory()->create();

    $this->get(route('foods.edit', $food))->assertForbidden();
});

test('edit food page is displayed', function () {
    $this->actingAs(User::factory()->admin()->create());
    $food = Food::factory()->create();

    $this->get(route('foods.edit', $food))->assertOk();
});

test('admin can update a food', function () {
    $this->actingAs(User::factory()->admin()->create());
    $food = Food::factory()->create(['name' => 'Zabkása', 'calories' => 300]);

    Livewire::test(Edit::class, ['food' => $food])
        ->set('name', 'Zabpehely')
        ->set('calories', '320')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('foods.index'));

    $food->refresh();
    expect($food->name)->toBe('Zabpehely');
    expect($food->calories)->toBe(320);
});

test('food name is required', function () {
    $this->actingAs(User::factory()->admin()->create());
    $food = Food::factory()->create();

    Livewire::test(Edit::class, ['food' => $food])
        ->set('name', '')
        ->call('save')
        ->assertHasErrors(['name' => 'required']);
});

test('food name must be unique', function () {
    $this->actingAs(User::factory()->admin()->create());

    Food::factory()->create(['name' => 'Zabkása']);
    $food = Food::factory()->create(['name' => 'Rizs']);

    Livewire::test(Edit::class, ['food' => $food])
        ->set('name', 'Zabkása')
        ->call('save')
        ->assertHasErrors(['name' => 'unique']);
});

test('food can keep its own name unchanged', function () {
    $this->actingAs(User::factory()->admin()->create());
    $food = Food::factory()->create(['name' => 'Zabkása']);

    Livewire::test(Edit::class, ['food' => $food])
        ->set('name', 'Zabkása')
        ->call('save')
        ->assertHasNoErrors();
});

test('existing nutrient values are pre-filled when editing', function () {
    $protein = createNutrient('protein', 'Fehérje');
    createNutrient('vitamin-c', 'C-vitamin', 'mg');

    $this->actingAs(User::factory()->admin()->create());
    $food = Food::factory()->withNutrition()->create();
    $food->nutrients()->updateExistingPivot($protein->id, ['value' => 12.5]);

    Livewire::test(Edit::class, ['food' => $food])
        ->assertSet('nutrientValues.protein', '12.50')
        ->assertSet('barcode', $food->barcode)
        ->assertSet('nutritionSynced', true);
});

test('admin can update the nutrient values', function () {
    $protein = createNutrient('protein', 'Fehérje');
    $vitaminC = createNutrient('vitamin-c', 'C-vitamin', 'mg');

    $this->actingAs(User::factory()->admin()->create());
    $food = Food::factory()->create();

    Livewire::test(Edit::class, ['food' => $food])
        ->set('nutrientValues.protein', '15')
        ->set('nutrientValues.vitamin-c', '8')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('foods.index'));

    $food->refresh();
    expect((float) $food->nutrients->find($protein)->pivot->value)->toBe(15.0);
    expect((float) $food->nutrients->find($vitaminC)->pivot->value)->toBe(8.0);
});

test('clearing a nutrient value removes it from the food', function () {
    $protein = createNutrient('protein', 'Fehérje');

    $this->actingAs(User::factory()->admin()->create());
    $food = Food::factory()->create();
    $food->nutrients()->attach($protein->id, ['value' => 10]);

    Livewire::test(Edit::class, ['food' => $food])
        ->set('nutrientValues.protein', '')
        ->call('save')
        ->assertHasNoErrors();

    expect($food->fresh('nutrients')->nutrients->pluck('id'))->not->toContain($protein->id);
});

test('food barcode must be unique when editing', function () {
    $this->actingAs(User::factory()->admin()->create());

    Food::factory()->create(['barcode' => '5901234123457']);
    $food = Food::factory()->create();

    Livewire::test(Edit::class, ['food' => $food])
        ->set('barcode', '5901234123457')
        ->call('save')
        ->assertHasErrors(['barcode' => 'unique']);
});

test('food can keep its own barcode unchanged', function () {
    $this->actingAs(User::factory()->admin()->create());
    $food = Food::factory()->create(['barcode' => '5901234123457']);

    Livewire::test(Edit::class, ['food' => $food])
        ->set('barcode', '5901234123457')
        ->call('save')
        ->assertHasNoErrors();
});
