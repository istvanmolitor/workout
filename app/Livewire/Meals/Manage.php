<?php

namespace App\Livewire\Meals;

use App\Models\Meal;
use App\Repositories\Contracts\MealRepositoryInterface;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Étkezések')]
class Manage extends Component
{
    protected MealRepositoryInterface $mealRepository;

    public function boot(MealRepositoryInterface $mealRepository): void
    {
        $this->mealRepository = $mealRepository;
    }

    /**
     * Get the authenticated user's meal entries.
     *
     * @return Collection<int, Meal>
     */
    #[Computed]
    public function meals(): Collection
    {
        return $this->mealRepository->forUser(Auth::user());
    }

    /**
     * Delete a meal entry.
     */
    public function delete(Meal $meal): void
    {
        $this->authorize('delete', $meal);

        $this->mealRepository->delete($meal);

        unset($this->meals);

        Flux::toast(variant: 'success', text: __('Meal entry deleted.'));
    }
}
