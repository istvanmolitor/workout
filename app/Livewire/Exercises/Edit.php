<?php

namespace App\Livewire\Exercises;

use App\Models\Exercise;
use App\Models\ExerciseCategory;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Gyakorlat szerkesztése')]
class Edit extends Component
{
    #[Locked]
    public Exercise $exercise;

    public string $name = '';

    public ?int $category_id = null;

    /**
     * Mount the component.
     */
    public function mount(Exercise $exercise): void
    {
        $this->exercise = $exercise;
        $this->name = $exercise->name;
        $this->category_id = $exercise->category_id;
    }

    /**
     * Get the categories available to choose from.
     *
     * @return Collection<int, ExerciseCategory>
     */
    #[Computed]
    public function categories(): Collection
    {
        return ExerciseCategory::query()->orderBy('name')->get();
    }

    /**
     * Update the exercise.
     */
    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('exercises', 'name')->ignore($this->exercise->id)],
            'category_id' => ['required', 'integer', 'exists:exercise_categories,id'],
        ]);

        $this->exercise->update($validated);

        Flux::toast(variant: 'success', text: __('Exercise updated.'));

        $this->redirectRoute('exercises.index', navigate: true);
    }
}
