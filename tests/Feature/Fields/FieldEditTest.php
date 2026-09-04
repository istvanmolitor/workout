<?php

use App\Livewire\Fields\Edit;
use App\Models\Field;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $field = Field::factory()->create();

    $this->get(route('fields.edit', $field))->assertRedirect(route('login'));
});

test('edit field page is displayed', function () {
    $this->actingAs(User::factory()->create());

    $field = Field::factory()->create();

    $this->get(route('fields.edit', $field))->assertOk();
});

test('field name and unit can be updated', function () {
    $this->actingAs(User::factory()->create());

    $field = Field::factory()->create(['name' => 'Súly', 'unit' => 'kg']);

    Livewire::test(Edit::class, ['field' => $field])
        ->set('name', 'Testsúly')
        ->set('unit', 'font')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('fields.index'));

    $field->refresh();

    expect($field->name)->toBe('Testsúly');
    expect($field->unit)->toBe('font');
});

test('field name is required', function () {
    $this->actingAs(User::factory()->create());

    $field = Field::factory()->create();

    Livewire::test(Edit::class, ['field' => $field])
        ->set('name', '')
        ->call('save')
        ->assertHasErrors(['name' => 'required']);
});

test('field name must be unique', function () {
    $this->actingAs(User::factory()->create());

    Field::factory()->create(['name' => 'Súly']);
    $field = Field::factory()->create(['name' => 'Táv']);

    Livewire::test(Edit::class, ['field' => $field])
        ->set('name', 'Súly')
        ->call('save')
        ->assertHasErrors(['name' => 'unique']);
});

test('field can keep its own name unchanged', function () {
    $this->actingAs(User::factory()->create());

    $field = Field::factory()->create(['name' => 'Súly']);

    Livewire::test(Edit::class, ['field' => $field])
        ->set('name', 'Súly')
        ->call('save')
        ->assertHasNoErrors();
});
