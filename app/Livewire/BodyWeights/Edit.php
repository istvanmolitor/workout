<?php

namespace App\Livewire\BodyWeights;

use App\Models\BodyWeight;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Testsúlymérés szerkesztése')]
class Edit extends Component
{
    #[Locked]
    public BodyWeight $bodyWeight;

    public string $weight = '';

    public string $measured_at = '';

    /**
     * Mount the component.
     */
    public function mount(BodyWeight $bodyWeight): void
    {
        $this->authorize('update', $bodyWeight);

        $this->bodyWeight = $bodyWeight;
        $this->weight = (string) $bodyWeight->weight;
        $this->measured_at = $bodyWeight->measured_at->toDateString();
    }

    /**
     * Update the body weight entry.
     */
    public function save(): void
    {
        $this->authorize('update', $this->bodyWeight);

        $validated = $this->validate([
            'weight' => ['required', 'numeric', 'min:1', 'max:500'],
            'measured_at' => [
                'required',
                'date',
                'before_or_equal:today',
                Rule::unique('body_weights', 'measured_at')->where('user_id', Auth::id())->ignore($this->bodyWeight->id),
            ],
        ]);

        $this->bodyWeight->update($validated);

        Flux::toast(variant: 'success', text: __('Body weight entry updated.'));

        $this->redirectRoute('body-weights.index', navigate: true);
    }
}
