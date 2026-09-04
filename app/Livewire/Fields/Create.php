<?php

namespace App\Livewire\Fields;

use App\Models\Field;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Új mező')]
class Create extends Component
{
    public string $name = '';

    public string $unit = '';

    /**
     * Create the field in the catalog.
     */
    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255', 'unique:fields,name'],
            'unit' => ['nullable', 'string', 'max:50'],
        ]);

        Field::query()->create([
            'name' => $validated['name'],
            'unit' => $validated['unit'] !== '' ? $validated['unit'] : null,
        ]);

        Flux::toast(variant: 'success', text: __('Field created.'));

        $this->redirectRoute('fields.index', navigate: true);
    }
}
