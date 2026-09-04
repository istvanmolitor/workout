<?php

namespace App\Livewire\ExerciseCategories;

use App\Models\ExerciseCategory;
use App\Repositories\Contracts\ExerciseCategoryRepositoryInterface;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Gyakorlatkategóriák')]
class Manage extends Component
{
    protected ExerciseCategoryRepositoryInterface $exerciseCategoryRepository;

    public function boot(ExerciseCategoryRepositoryInterface $exerciseCategoryRepository): void
    {
        $this->exerciseCategoryRepository = $exerciseCategoryRepository;
    }

    /**
     * Get all exercise categories.
     *
     * @return Collection<int, ExerciseCategory>
     */
    #[Computed]
    public function exerciseCategories(): Collection
    {
        return $this->exerciseCategoryRepository->all();
    }

    /**
     * Delete an exercise category.
     */
    public function delete(ExerciseCategory $exerciseCategory): void
    {
        $this->exerciseCategoryRepository->delete($exerciseCategory);

        unset($this->exerciseCategories);

        Flux::toast(variant: 'success', text: __('Exercise category deleted.'));
    }
}
