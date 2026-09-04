<?php

namespace App\Livewire\ExerciseCategories;

use App\Repositories\Contracts\ExerciseCategoryRepositoryInterface;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Új gyakorlatkategória')]
class Create extends Component
{
    protected ExerciseCategoryRepositoryInterface $exerciseCategoryRepository;

    public string $name = '';

    public function boot(ExerciseCategoryRepositoryInterface $exerciseCategoryRepository): void
    {
        $this->exerciseCategoryRepository = $exerciseCategoryRepository;
    }

    /**
     * Create the exercise category.
     */
    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255', 'unique:exercise_categories,name'],
        ]);

        $this->exerciseCategoryRepository->create($validated);

        Flux::toast(variant: 'success', text: __('Exercise category created.'));

        $this->redirectRoute('exercise-categories.index', navigate: true);
    }
}
