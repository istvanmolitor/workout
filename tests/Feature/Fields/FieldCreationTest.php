<?php

use App\Livewire\Fields\Create;
use App\Models\Field;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $this->get(route('fields.create'))->assertRedirect(route('login'));
});

test('create field page is displayed', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('fields.create'))->assertOk();
});

test('authenticated user can create a field', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(Create::class)
        ->set('name', 'Súly')
        ->set('unit', 'kg')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('fields.index'));

    expect(Field::query()->where('name', 'Súly')->where('unit', 'kg')->exists())->toBeTrue();
});

test('a field can be created without a unit', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(Create::class)
        ->set('name', 'Ismétlés')
        ->set('unit', '')
        ->call('save')
        ->assertHasNoErrors();

    expect(Field::query()->where('name', 'Ismétlés')->whereNull('unit')->exists())->toBeTrue();
});

test('field name is required', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(Create::class)
        ->set('name', '')
        ->call('save')
        ->assertHasErrors(['name' => 'required']);
});

test('field name must be unique', function () {
    $this->actingAs(User::factory()->create());

    Field::factory()->create(['name' => 'Súly']);

    Livewire::test(Create::class)
        ->set('name', 'Súly')
        ->call('save')
        ->assertHasErrors(['name' => 'unique']);
});
