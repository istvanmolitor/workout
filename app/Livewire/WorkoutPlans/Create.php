<?php

namespace App\Livewire\WorkoutPlans;

use App\Models\Exercise;
use App\Models\Field;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Új edzésterv')]
class Create extends Component
{
    public string $name = '';

    public string $description = '';

    /**
     * @var array<int, array{exercise_id: int|string, sets: array<int, array{values: array<int, int|string|null>}>}>
     */
    public array $exercises = [
        ['exercise_id' => '', 'sets' => [['values' => []], ['values' => []], ['values' => []]]],
    ];

    /**
     * Get the exercises available to choose from.
     *
     * @return Collection<int, Exercise>
     */
    #[Computed]
    public function availableExercises(): Collection
    {
        return Exercise::query()->with('exerciseType.fields.field')->orderBy('name')->get();
    }

    /**
     * Get the fields tracked by the given exercise's type, in display order, keyed by field id.
     *
     * @return SupportCollection<int, Field>
     */
    public function fieldsForExercise(int|string $exerciseId): SupportCollection
    {
        $exercise = $this->availableExercises->firstWhere('id', (int) $exerciseId);

        if (! $exercise) {
            return collect();
        }

        return $exercise->exerciseType->fields->pluck('field', 'field.id');
    }

    /**
     * Determine whether the given exercise's type allows only a single set.
     */
    public function isSingleSet(int|string $exerciseId): bool
    {
        return (bool) $this->availableExercises->firstWhere('id', (int) $exerciseId)?->exerciseType->single_set;
    }

    /**
     * Sync a row's set values to match the fields of its currently selected exercise.
     */
    private function syncSetValues(int $exerciseIndex): void
    {
        $exerciseId = $this->exercises[$exerciseIndex]['exercise_id'];
        $fieldIds = $this->fieldsForExercise($exerciseId)->keys();

        if ($this->isSingleSet($exerciseId)) {
            $this->exercises[$exerciseIndex]['sets'] = [$this->exercises[$exerciseIndex]['sets'][0] ?? ['values' => []]];
        }

        foreach ($this->exercises[$exerciseIndex]['sets'] as $setIndex => $set) {
            $this->exercises[$exerciseIndex]['sets'][$setIndex]['values'] = $fieldIds
                ->mapWithKeys(fn (int $fieldId) => [$fieldId => $set['values'][$fieldId] ?? null])
                ->all();
        }
    }

    /**
     * React to a row's exercise selection changing by refreshing its set fields.
     */
    public function updated(string $name): void
    {
        if (preg_match('/^exercises\.(\d+)\.exercise_id$/', $name, $matches)) {
            $this->syncSetValues((int) $matches[1]);
        }
    }

    /**
     * Add an empty exercise row to the form.
     */
    public function addExercise(): void
    {
        $this->exercises[] = ['exercise_id' => '', 'sets' => [['values' => []], ['values' => []], ['values' => []]]];
    }

    /**
     * Remove an exercise row from the form.
     */
    public function removeExercise(int $index): void
    {
        unset($this->exercises[$index]);

        $this->exercises = array_values($this->exercises);
    }

    /**
     * Add an empty set row to an exercise, matching its currently tracked fields.
     */
    public function addSet(int $exerciseIndex): void
    {
        if ($this->isSingleSet($this->exercises[$exerciseIndex]['exercise_id'])) {
            return;
        }

        $fieldIds = $this->fieldsForExercise($this->exercises[$exerciseIndex]['exercise_id'])->keys();

        $this->exercises[$exerciseIndex]['sets'][] = [
            'values' => $fieldIds->mapWithKeys(fn (int $fieldId) => [$fieldId => null])->all(),
        ];
    }

    /**
     * Remove a set row from an exercise.
     */
    public function removeSet(int $exerciseIndex, int $setIndex): void
    {
        unset($this->exercises[$exerciseIndex]['sets'][$setIndex]);

        $this->exercises[$exerciseIndex]['sets'] = array_values($this->exercises[$exerciseIndex]['sets']);
    }

    /**
     * Create the workout plan for the authenticated user.
     */
    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'exercises' => ['required', 'array', 'min:1'],
            'exercises.*.exercise_id' => ['required', 'integer', 'exists:exercises,id'],
            'exercises.*.sets' => ['required', 'array', 'min:1'],
            'exercises.*.sets.*.values.*' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
        ]);

        foreach ($this->exercises as $index => $exercise) {
            if ($this->isSingleSet($exercise['exercise_id']) && count($exercise['sets']) !== 1) {
                $this->addError("exercises.{$index}.sets", __('This exercise type only allows a single set.'));
            }
        }

        if ($this->getErrorBag()->isNotEmpty()) {
            return;
        }

        $workoutPlan = Auth::user()->workoutPlans()->create([
            'name' => $validated['name'],
            'description' => $validated['description'],
        ]);

        foreach ($this->exercises as $order => $exercise) {
            $planExercise = $workoutPlan->exercises()->create([
                'exercise_id' => $exercise['exercise_id'],
                'order' => $order,
            ]);

            $fieldIds = $this->fieldsForExercise($exercise['exercise_id'])->keys();

            foreach ($exercise['sets'] as $setOrder => $set) {
                $planSet = $planExercise->sets()->create(['order' => $setOrder]);

                foreach ($fieldIds as $fieldId) {
                    $value = $set['values'][$fieldId] ?? null;

                    if ($value === null || $value === '') {
                        continue;
                    }

                    $planSet->values()->create(['field_id' => $fieldId, 'value' => $value]);
                }
            }
        }

        Flux::toast(variant: 'success', text: __('Workout plan created.'));

        $this->redirectRoute('workout-plans.index', navigate: true);
    }
}
