<?php

namespace App\Livewire\Foods;

use App\Models\Food;
use App\Repositories\Contracts\FoodRepositoryInterface;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Ételek')]
class Manage extends Component
{
    protected FoodRepositoryInterface $foodRepository;

    public string $search = '';

    public function boot(FoodRepositoryInterface $foodRepository): void
    {
        $this->foodRepository = $foodRepository;
    }

    /**
     * Get the foods in the catalog matching the current search term.
     *
     * @return Collection<int, Food>
     */
    #[Computed]
    public function foods(): Collection
    {
        return $this->foodRepository->all($this->search);
    }

    /**
     * Delete a food from the catalog.
     */
    public function delete(Food $food): void
    {
        try {
            $this->foodRepository->delete($food);
        } catch (QueryException) {
            Flux::toast(variant: 'danger', text: __('This food is used in a meal and cannot be deleted.'));

            return;
        }

        unset($this->foods);

        Flux::toast(variant: 'success', text: __('Food deleted.'));
    }
}
