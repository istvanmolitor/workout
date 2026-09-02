<?php

namespace App\Livewire\Exercises;

use App\Models\Exercise;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('New exercise')]
class Create extends Component
{
    public string $name = '';

    /**
     * Create the exercise in the catalog.
     */
    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255', 'unique:exercises,name'],
        ]);

        Exercise::query()->create($validated);

        Flux::toast(variant: 'success', text: __('Exercise created.'));

        $this->redirectRoute('exercises.index', navigate: true);
    }
}
