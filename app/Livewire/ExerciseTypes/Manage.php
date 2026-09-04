<?php

namespace App\Livewire\ExerciseTypes;

use App\Models\ExerciseType;
use App\Repositories\Contracts\ExerciseTypeRepositoryInterface;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Gyakorlattípusok')]
class Manage extends Component
{
    protected ExerciseTypeRepositoryInterface $exerciseTypeRepository;

    public function boot(ExerciseTypeRepositoryInterface $exerciseTypeRepository): void
    {
        $this->exerciseTypeRepository = $exerciseTypeRepository;
    }

    /**
     * Get all exercise types with their tracked fields.
     *
     * @return Collection<int, ExerciseType>
     */
    #[Computed]
    public function exerciseTypes(): Collection
    {
        return $this->exerciseTypeRepository->allWithFields();
    }

    /**
     * Delete an exercise type from the catalog.
     */
    public function delete(ExerciseType $exerciseType): void
    {
        try {
            $this->exerciseTypeRepository->delete($exerciseType);
        } catch (QueryException) {
            Flux::toast(variant: 'danger', text: __('This exercise type is used by an exercise and cannot be deleted.'));

            return;
        }

        unset($this->exerciseTypes);

        Flux::toast(variant: 'success', text: __('Exercise type deleted.'));
    }
}
