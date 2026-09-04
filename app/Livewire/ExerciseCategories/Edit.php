<?php

namespace App\Livewire\ExerciseCategories;

use App\Models\ExerciseCategory;
use App\Repositories\Contracts\ExerciseCategoryRepositoryInterface;
use Flux\Flux;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Gyakorlatkategória szerkesztése')]
class Edit extends Component
{
    protected ExerciseCategoryRepositoryInterface $exerciseCategoryRepository;

    #[Locked]
    public ExerciseCategory $exerciseCategory;

    public string $name = '';

    public function boot(ExerciseCategoryRepositoryInterface $exerciseCategoryRepository): void
    {
        $this->exerciseCategoryRepository = $exerciseCategoryRepository;
    }

    /**
     * Mount the component.
     */
    public function mount(ExerciseCategory $exerciseCategory): void
    {
        $this->exerciseCategory = $exerciseCategory;
        $this->name = $exerciseCategory->name;
    }

    /**
     * Update the exercise category.
     */
    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('exercise_categories', 'name')->ignore($this->exerciseCategory->id)],
        ]);

        $this->exerciseCategoryRepository->update($this->exerciseCategory, $validated);

        Flux::toast(variant: 'success', text: __('Exercise category updated.'));

        $this->redirectRoute('exercise-categories.index', navigate: true);
    }
}
