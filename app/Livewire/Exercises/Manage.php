<?php

namespace App\Livewire\Exercises;

use App\Models\Exercise;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Gyakorlatok')]
class Manage extends Component
{
    /**
     * Get all exercises in the catalog.
     *
     * @return Collection<int, Exercise>
     */
    #[Computed]
    public function exercises(): Collection
    {
        return Exercise::query()->with('category')->orderBy('name')->get();
    }

    /**
     * Delete an exercise from the catalog.
     */
    public function delete(Exercise $exercise): void
    {
        try {
            $exercise->delete();
        } catch (QueryException) {
            Flux::toast(variant: 'danger', text: __('This exercise is used in a workout plan and cannot be deleted.'));

            return;
        }

        unset($this->exercises);

        Flux::toast(variant: 'success', text: __('Exercise deleted.'));
    }
}
