<?php

use App\Livewire\ExerciseTypes\Edit;
use App\Models\ExerciseType;
use App\Models\ExerciseTypeField;
use App\Models\Field;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $exerciseType = ExerciseType::factory()->create();

    $this->get(route('exercise-types.edit', $exerciseType))->assertRedirect(route('login'));
});

test('edit exercise type page is displayed', function () {
    $this->actingAs(User::factory()->create());

    $exerciseType = ExerciseType::factory()->create();

    $this->get(route('exercise-types.edit', $exerciseType))->assertOk();
});

test('owner can update the exercise type name and its fields', function () {
    $this->actingAs(User::factory()->create());

    $exerciseType = ExerciseType::factory()->create(['name' => 'Futás']);
    $oldField = Field::factory()->create();
    ExerciseTypeField::factory()->create(['exercise_type_id' => $exerciseType->id, 'field_id' => $oldField->id]);
    $distance = Field::factory()->create(['name' => 'Táv']);
    $time = Field::factory()->create(['name' => 'Idő']);

    Livewire::test(Edit::class, ['exerciseType' => $exerciseType])
        ->set('name', 'Kocogás')
        ->set('fields', [
            ['field_id' => $distance->id],
            ['field_id' => $time->id],
        ])
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('exercise-types.index'));

    $exerciseType->refresh();

    expect($exerciseType->name)->toBe('Kocogás');
    expect($exerciseType->fields->pluck('field_id')->all())->toBe([$distance->id, $time->id]);
});

test('exercise type name is required', function () {
    $this->actingAs(User::factory()->create());

    $exerciseType = ExerciseType::factory()->create();
    ExerciseTypeField::factory()->create(['exercise_type_id' => $exerciseType->id]);

    Livewire::test(Edit::class, ['exerciseType' => $exerciseType])
        ->set('name', '')
        ->call('save')
        ->assertHasErrors(['name' => 'required']);
});

test('exercise type name must be unique', function () {
    $this->actingAs(User::factory()->create());

    ExerciseType::factory()->create(['name' => 'Futás']);
    $exerciseType = ExerciseType::factory()->create(['name' => 'Úszás']);
    ExerciseTypeField::factory()->create(['exercise_type_id' => $exerciseType->id]);

    Livewire::test(Edit::class, ['exerciseType' => $exerciseType])
        ->set('name', 'Futás')
        ->call('save')
        ->assertHasErrors(['name' => 'unique']);
});

test('the single set flag can be toggled', function () {
    $this->actingAs(User::factory()->create());

    $exerciseType = ExerciseType::factory()->create(['single_set' => false]);
    ExerciseTypeField::factory()->create(['exercise_type_id' => $exerciseType->id]);

    Livewire::test(Edit::class, ['exerciseType' => $exerciseType])
        ->set('single_set', true)
        ->call('save')
        ->assertHasNoErrors();

    expect($exerciseType->refresh()->single_set)->toBeTrue();
});

test('exercise type can keep its own name unchanged', function () {
    $this->actingAs(User::factory()->create());

    $exerciseType = ExerciseType::factory()->create(['name' => 'Futás']);
    ExerciseTypeField::factory()->create(['exercise_type_id' => $exerciseType->id]);

    Livewire::test(Edit::class, ['exerciseType' => $exerciseType])
        ->set('name', 'Futás')
        ->call('save')
        ->assertHasNoErrors();
});
