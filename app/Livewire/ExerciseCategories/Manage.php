<?php

namespace App\Livewire\ExerciseCategories;

use App\Models\ExerciseCategory;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Gyakorlatkategóriák')]
class Manage extends Component
{
    /**
     * Get all exercise categories.
     *
     * @return Collection<int, ExerciseCategory>
     */
    #[Computed]
    public function exerciseCategories(): Collection
    {
        return ExerciseCategory::query()->orderBy('name')->get();
    }

    /**
     * Delete an exercise category.
     */
    public function delete(ExerciseCategory $exerciseCategory): void
    {
        $exerciseCategory->delete();

        unset($this->exerciseCategories);

        Flux::toast(variant: 'success', text: __('Exercise category deleted.'));
    }
}
