<?php

namespace App\Livewire\ExerciseTypes;

use App\Models\ExerciseType;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Gyakorlattípusok')]
class Manage extends Component
{
    /**
     * Get all exercise types with their tracked fields.
     *
     * @return Collection<int, ExerciseType>
     */
    #[Computed]
    public function exerciseTypes(): Collection
    {
        return ExerciseType::query()->with('fields.field')->orderBy('name')->get();
    }

    /**
     * Delete an exercise type from the catalog.
     */
    public function delete(ExerciseType $exerciseType): void
    {
        try {
            $exerciseType->delete();
        } catch (QueryException) {
            Flux::toast(variant: 'danger', text: __('This exercise type is used by an exercise and cannot be deleted.'));

            return;
        }

        unset($this->exerciseTypes);

        Flux::toast(variant: 'success', text: __('Exercise type deleted.'));
    }
}
