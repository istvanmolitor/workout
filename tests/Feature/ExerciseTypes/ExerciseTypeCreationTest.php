<?php

use App\Livewire\ExerciseTypes\Create;
use App\Models\ExerciseType;
use App\Models\Field;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $this->get(route('exercise-types.create'))->assertRedirect(route('login'));
});

test('create exercise type page is displayed', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('exercise-types.create'))->assertOk();
});

test('authenticated user can create an exercise type with ordered fields', function () {
    $this->actingAs(User::factory()->create());

    $distance = Field::factory()->create(['name' => 'Táv']);
    $time = Field::factory()->create(['name' => 'Idő']);

    Livewire::test(Create::class)
        ->set('name', 'Futás')
        ->set('fields', [
            ['field_id' => $distance->id],
            ['field_id' => $time->id],
        ])
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('exercise-types.index'));

    $exerciseType = ExerciseType::query()->where('name', 'Futás')->sole();

    expect($exerciseType->fields->pluck('field_id')->all())->toBe([$distance->id, $time->id]);
    expect($exerciseType->single_set)->toBeFalse();
});

test('an exercise type can be marked as single set', function () {
    $this->actingAs(User::factory()->create());

    $field = Field::factory()->create();

    Livewire::test(Create::class)
        ->set('name', 'Plank')
        ->set('single_set', true)
        ->set('fields', [['field_id' => $field->id]])
        ->call('save')
        ->assertHasNoErrors();

    expect(ExerciseType::query()->where('name', 'Plank')->sole()->single_set)->toBeTrue();
});

test('exercise type name is required', function () {
    $this->actingAs(User::factory()->create());
    $field = Field::factory()->create();

    Livewire::test(Create::class)
        ->set('name', '')
        ->set('fields', [['field_id' => $field->id]])
        ->call('save')
        ->assertHasErrors(['name' => 'required']);
});

test('exercise type name must be unique', function () {
    $this->actingAs(User::factory()->create());

    ExerciseType::factory()->create(['name' => 'Futás']);
    $field = Field::factory()->create();

    Livewire::test(Create::class)
        ->set('name', 'Futás')
        ->set('fields', [['field_id' => $field->id]])
        ->call('save')
        ->assertHasErrors(['name' => 'unique']);
});

test('at least one field is required', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(Create::class)
        ->set('name', 'Futás')
        ->set('fields', [['field_id' => '']])
        ->call('save')
        ->assertHasErrors(['fields.0.field_id' => 'required']);
});

test('the same field cannot be selected twice', function () {
    $this->actingAs(User::factory()->create());

    $field = Field::factory()->create();

    Livewire::test(Create::class)
        ->set('name', 'Futás')
        ->set('fields', [
            ['field_id' => $field->id],
            ['field_id' => $field->id],
        ])
        ->call('save')
        ->assertHasErrors(['fields.0.field_id' => 'distinct']);
});
