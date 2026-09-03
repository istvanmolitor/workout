<?php

namespace App\Livewire\BodyWeights;

use App\Models\BodyWeight;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Testsúly')]
class Manage extends Component
{
    /**
     * Get the authenticated user's body weight entries.
     *
     * @return Collection<int, BodyWeight>
     */
    #[Computed]
    public function bodyWeights(): Collection
    {
        return Auth::user()->bodyWeights()->latest('measured_at')->get();
    }

    /**
     * Delete a body weight entry.
     */
    public function delete(BodyWeight $bodyWeight): void
    {
        $this->authorize('delete', $bodyWeight);

        $bodyWeight->delete();

        unset($this->bodyWeights);

        Flux::toast(variant: 'success', text: __('Body weight entry deleted.'));
    }
}
