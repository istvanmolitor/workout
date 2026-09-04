<?php

namespace App\Livewire\ExerciseTypes;

use App\Models\Field;
use App\Repositories\Contracts\ExerciseTypeRepositoryInterface;
use App\Repositories\Contracts\FieldRepositoryInterface;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Új gyakorlattípus')]
class Create extends Component
{
    protected ExerciseTypeRepositoryInterface $exerciseTypeRepository;

    protected FieldRepositoryInterface $fieldRepository;

    public string $name = '';

    public bool $single_set = false;

    /**
     * @var array<int, array{field_id: int|string}>
     */
    public array $fields = [
        ['field_id' => ''],
    ];

    public function boot(ExerciseTypeRepositoryInterface $exerciseTypeRepository, FieldRepositoryInterface $fieldRepository): void
    {
        $this->exerciseTypeRepository = $exerciseTypeRepository;
        $this->fieldRepository = $fieldRepository;
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
     * Create the exercise type.
     */
    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255', 'unique:exercise_types,name'],
            'single_set' => ['boolean'],
            'fields' => ['required', 'array', 'min:1'],
            'fields.*.field_id' => ['required', 'integer', 'distinct', 'exists:fields,id'],
        ]);

        $this->exerciseTypeRepository->create([
            'name' => $validated['name'],
            'single_set' => $validated['single_set'],
        ], $validated['fields']);

        Flux::toast(variant: 'success', text: __('Exercise type created.'));

        $this->redirectRoute('exercise-types.index', navigate: true);
    }
}
