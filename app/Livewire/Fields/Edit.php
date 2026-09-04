<?php

namespace App\Livewire\Fields;

use App\Models\Field;
use App\Repositories\Contracts\FieldRepositoryInterface;
use Flux\Flux;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Mező szerkesztése')]
class Edit extends Component
{
    protected FieldRepositoryInterface $fieldRepository;

    #[Locked]
    public Field $field;

    public string $name = '';

    public string $unit = '';

    public function boot(FieldRepositoryInterface $fieldRepository): void
    {
        $this->fieldRepository = $fieldRepository;
    }

    /**
     * Mount the component.
     */
    public function mount(Field $field): void
    {
        $this->field = $field;
        $this->name = $field->name;
        $this->unit = $field->unit ?? '';
    }

    /**
     * Update the field.
     */
    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('fields', 'name')->ignore($this->field->id)],
            'unit' => ['nullable', 'string', 'max:50'],
        ]);

        $this->fieldRepository->update($this->field, [
            'name' => $validated['name'],
            'unit' => $validated['unit'] !== '' ? $validated['unit'] : null,
        ]);

        Flux::toast(variant: 'success', text: __('Field updated.'));

        $this->redirectRoute('fields.index', navigate: true);
    }
}
