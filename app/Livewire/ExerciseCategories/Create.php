<?php

namespace App\Livewire\ExerciseCategories;

use App\Models\ExerciseCategory;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Új gyakorlatkategória')]
class Create extends Component
{
    public string $name = '';

    /**
     * Create the exercise category.
     */
    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255', 'unique:exercise_categories,name'],
        ]);

        ExerciseCategory::query()->create($validated);

        Flux::toast(variant: 'success', text: __('Exercise category created.'));

        $this->redirectRoute('exercise-categories.index', navigate: true);
    }
}
