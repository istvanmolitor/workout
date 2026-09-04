<?php

namespace App\Livewire\ExerciseTypes;

use App\Models\ExerciseType;
use App\Models\Field;
use App\Repositories\Contracts\ExerciseTypeRepositoryInterface;
use App\Repositories\Contracts\FieldRepositoryInterface;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Gyakorlattípus szerkesztése')]
class Edit extends Component
{
    protected ExerciseTypeRepositoryInterface $exerciseTypeRepository;

    protected FieldRepositoryInterface $fieldRepository;

    #[Locked]
    public ExerciseType $exerciseType;

    public string $name = '';

    public bool $single_set = false;

    /**
     * @var array<int, array{field_id: int|string}>
     */
    public array $fields = [];

    public function boot(ExerciseTypeRepositoryInterface $exerciseTypeRepository, FieldRepositoryInterface $fieldRepository): void
    {
        $this->exerciseTypeRepository = $exerciseTypeRepository;
        $this->fieldRepository = $fieldRepository;
    }

    /**
     * Mount the component.
     */
    public function mount(ExerciseType $exerciseType): void
    {
        $this->exerciseType = $exerciseType;
        $this->name = $exerciseType->name;
        $this->single_set = $exerciseType->single_set;

        $this->fields = $exerciseType->fields
            ->map(fn ($typeField) => ['field_id' => $typeField->field_id])
            ->all();

        if ($this->fields === []) {
            $this->fields = [['field_id' => '']];
        }
    }

    /**
     * Get the fields available to choose from.
     *
     * @return Collection<int, Field>
     */
    #[Computed]
    public function availableFields(): Collection
    {
        return $this->fieldRepository->all();
    }

    /**
     * Add an empty field row to the form.
     */
    public function addField(): void
    {
        $this->fields[] = ['field_id' => ''];
    }

    /**
     * Remove a field row from the form.
     */
    public function removeField(int $index): void
    {
        unset($this->fields[$index]);

        $this->fields = array_values($this->fields);
    }

    /**
     * Update the exercise type.
     */
    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('exercise_types', 'name')->ignore($this->exerciseType->id)],
            'single_set' => ['boolean'],
            'fields' => ['required', 'array', 'min:1'],
            'fields.*.field_id' => ['required', 'integer', 'distinct', 'exists:fields,id'],
        ]);

        $this->exerciseTypeRepository->update($this->exerciseType, [
            'name' => $validated['name'],
            'single_set' => $validated['single_set'],
        ], $validated['fields']);

        Flux::toast(variant: 'success', text: __('Exercise type updated.'));

        $this->redirectRoute('exercise-types.index', navigate: true);
    }
}
