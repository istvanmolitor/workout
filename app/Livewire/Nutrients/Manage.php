<?php

namespace App\Livewire\Nutrients;

use App\Models\Nutrient;
use App\Repositories\Contracts\NutrientRepositoryInterface;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Tápanyagok')]
class Manage extends Component
{
    protected NutrientRepositoryInterface $nutrientRepository;

    public function boot(NutrientRepositoryInterface $nutrientRepository): void
    {
        $this->nutrientRepository = $nutrientRepository;
    }

    /**
     * Get all nutrients in the catalog.
     *
     * @return Collection<int, Nutrient>
     */
    #[Computed]
    public function nutrients(): Collection
    {
        return $this->nutrientRepository->all();
    }

    /**
     * Delete a nutrient from the catalog.
     */
    public function delete(Nutrient $nutrient): void
    {
        try {
            $this->nutrientRepository->delete($nutrient);
        } catch (QueryException) {
            Flux::toast(variant: 'danger', text: __('This nutrient has recorded values and cannot be deleted.'));

            return;
        }

        unset($this->nutrients);

        Flux::toast(variant: 'success', text: __('Nutrient deleted.'));
    }
}
