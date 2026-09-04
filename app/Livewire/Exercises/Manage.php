<?php

namespace App\Livewire\Exercises;

use App\Models\Exercise;
use App\Repositories\Contracts\ExerciseRepositoryInterface;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Gyakorlatok')]
class Manage extends Component
{
    protected ExerciseRepositoryInterface $exerciseRepository;

    public function boot(ExerciseRepositoryInterface $exerciseRepository): void
    {
        $this->exerciseRepository = $exerciseRepository;
    }

    /**
     * Get all exercises in the catalog, grouped by category name.
     *
     * @return Collection<string, Collection<int, Exercise>>
     */
    #[Computed]
    public function exercises(): Collection
    {
        return $this->exerciseRepository->allGroupedByCategory();
    }

    /**
     * Delete an exercise from the catalog.
     */
    public function delete(Exercise $exercise): void
    {
        try {
            $this->exerciseRepository->delete($exercise);
        } catch (QueryException) {
            Flux::toast(variant: 'danger', text: __('This exercise is used in a workout plan and cannot be deleted.'));

            return;
        }

        unset($this->exercises);

        Flux::toast(variant: 'success', text: __('Exercise deleted.'));
    }
}
