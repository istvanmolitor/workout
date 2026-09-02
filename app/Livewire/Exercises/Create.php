<?php

namespace App\Livewire\Exercises;

use App\Models\Exercise;
use App\Models\ExerciseCategory;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Új gyakorlat')]
class Create extends Component
{
    public string $name = '';

    public ?int $category_id = null;

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
     * Create the exercise in the catalog.
     */
    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255', 'unique:exercises,name'],
            'category_id' => ['required', 'integer', 'exists:exercise_categories,id'],
        ]);

        Exercise::query()->create($validated);

        Flux::toast(variant: 'success', text: __('Exercise created.'));

        $this->redirectRoute('exercises.index', navigate: true);
    }
}
