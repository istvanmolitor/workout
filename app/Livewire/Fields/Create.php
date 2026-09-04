<?php

namespace App\Livewire\Fields;

use App\Repositories\Contracts\FieldRepositoryInterface;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Új mező')]
class Create extends Component
{
    protected FieldRepositoryInterface $fieldRepository;

    public string $name = '';

    public string $unit = '';

    public function boot(FieldRepositoryInterface $fieldRepository): void
    {
        $this->fieldRepository = $fieldRepository;
    }

    /**
     * Create the field in the catalog.
     */
    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255', 'unique:fields,name'],
            'unit' => ['nullable', 'string', 'max:50'],
        ]);

        $this->fieldRepository->create([
            'name' => $validated['name'],
            'unit' => $validated['unit'] !== '' ? $validated['unit'] : null,
        ]);

        Flux::toast(variant: 'success', text: __('Field created.'));

        $this->redirectRoute('fields.index', navigate: true);
    }
}
