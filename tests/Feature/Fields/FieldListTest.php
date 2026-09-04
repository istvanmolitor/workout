<?php

use App\Livewire\Fields\Manage;
use App\Models\ExerciseType;
use App\Models\ExerciseTypeField;
use App\Models\Field;
use App\Models\User;
use App\Models\WorkoutExerciseSetValue;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $this->get(route('fields.index'))->assertRedirect(route('login'));
});

test('fields page is displayed', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('fields.index'))->assertOk();
});

test('lists fields', function () {
    $this->actingAs(User::factory()->create());

    Field::factory()->create(['name' => 'Súly', 'unit' => 'kg']);
    Field::factory()->create(['name' => 'Táv', 'unit' => 'km']);

    Livewire::test(Manage::class)
        ->assertSee('Súly')
        ->assertSee('Táv');
});

test('an unused field can be deleted', function () {
    $this->actingAs(User::factory()->create());

    $field = Field::factory()->create();

    Livewire::test(Manage::class)
        ->call('delete', $field->id);

    expect(Field::query()->find($field->id))->toBeNull();
});

test('deleting a field removes it from the exercise types tracking it', function () {
    $this->actingAs(User::factory()->create());

    $field = Field::factory()->create();
    $exerciseType = ExerciseType::factory()->create();
    ExerciseTypeField::factory()->create(['exercise_type_id' => $exerciseType->id, 'field_id' => $field->id]);

    Livewire::test(Manage::class)
        ->call('delete', $field->id);

    expect(ExerciseTypeField::query()->where('field_id', $field->id)->count())->toBe(0);
});

test('a field with recorded values cannot be deleted', function () {
    $this->actingAs(User::factory()->create());

    $field = Field::factory()->create();
    WorkoutExerciseSetValue::factory()->create(['field_id' => $field->id]);

    Livewire::test(Manage::class)
        ->call('delete', $field->id);

    expect(Field::query()->find($field->id))->not->toBeNull();
});
