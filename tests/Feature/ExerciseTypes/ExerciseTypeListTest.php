<?php

use App\Livewire\ExerciseTypes\Manage;
use App\Models\Exercise;
use App\Models\ExerciseType;
use App\Models\ExerciseTypeField;
use App\Models\Field;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $this->get(route('exercise-types.index'))->assertRedirect(route('login'));
});

test('exercise types page is displayed', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('exercise-types.index'))->assertOk();
});

test('lists exercise types with their fields', function () {
    $this->actingAs(User::factory()->create());

    $exerciseType = ExerciseType::factory()->create(['name' => 'Futás']);
    $field = Field::factory()->create(['name' => 'Táv']);
    ExerciseTypeField::factory()->create(['exercise_type_id' => $exerciseType->id, 'field_id' => $field->id]);

    Livewire::test(Manage::class)
        ->assertSee('Futás')
        ->assertSee('Táv');
});

test('an unused exercise type can be deleted', function () {
    $this->actingAs(User::factory()->create());

    $exerciseType = ExerciseType::factory()->create();

    Livewire::test(Manage::class)
        ->call('delete', $exerciseType->id);

    expect(ExerciseType::query()->find($exerciseType->id))->toBeNull();
});

test('deleting an exercise type removes its field associations', function () {
    $this->actingAs(User::factory()->create());

    $exerciseType = ExerciseType::factory()->create();
    ExerciseTypeField::factory()->create(['exercise_type_id' => $exerciseType->id]);

    Livewire::test(Manage::class)->call('delete', $exerciseType->id);

    expect(ExerciseTypeField::query()->where('exercise_type_id', $exerciseType->id)->count())->toBe(0);
});

test('an exercise type used by an exercise cannot be deleted', function () {
    $this->actingAs(User::factory()->create());

    $exerciseType = ExerciseType::factory()->create();
    Exercise::factory()->create(['exercise_type_id' => $exerciseType->id]);

    Livewire::test(Manage::class)->call('delete', $exerciseType->id);

    expect(ExerciseType::query()->find($exerciseType->id))->not->toBeNull();
});
