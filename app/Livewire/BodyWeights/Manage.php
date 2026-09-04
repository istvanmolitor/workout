<?php

namespace App\Livewire\BodyWeights;

use App\Models\BodyWeight;
use App\Repositories\Contracts\BodyWeightRepositoryInterface;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Testsúly')]
class Manage extends Component
{
    protected BodyWeightRepositoryInterface $bodyWeightRepository;

    public function boot(BodyWeightRepositoryInterface $bodyWeightRepository): void
    {
        $this->bodyWeightRepository = $bodyWeightRepository;
    }

    /**
     * Get the authenticated user's body weight entries.
     *
     * @return Collection<int, BodyWeight>
     */
    #[Computed]
    public function bodyWeights(): Collection
    {
        return $this->bodyWeightRepository->forUser(Auth::user());
    }

    /**
     * Delete a body weight entry.
     */
    public function delete(BodyWeight $bodyWeight): void
    {
        $this->authorize('delete', $bodyWeight);

        $this->bodyWeightRepository->delete($bodyWeight);

        unset($this->bodyWeights);

        Flux::toast(variant: 'success', text: __('Body weight entry deleted.'));
    }
}
