<?php

namespace App\Livewire\Exercises;

use App\Models\Exercise;
use Flux\Flux;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Gyakorlat szerkesztése')]
class Edit extends Component
{
    #[Locked]
    public Exercise $exercise;

    public string $name = '';

    /**
     * Mount the component.
     */
    public function mount(Exercise $exercise): void
    {
        $this->exercise = $exercise;
        $this->name = $exercise->name;
    }

    /**
     * Update the exercise.
     */
    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('exercises', 'name')->ignore($this->exercise->id)],
        ]);

        $this->exercise->update($validated);

        Flux::toast(variant: 'success', text: __('Exercise updated.'));

        $this->redirectRoute('exercises.index', navigate: true);
    }
}
