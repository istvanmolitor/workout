<?php

namespace App\Livewire\BodyWeights;

use App\Repositories\Contracts\BodyWeightRepositoryInterface;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Új testsúlymérés')]
class Create extends Component
{
    protected BodyWeightRepositoryInterface $bodyWeightRepository;

    public string $weight = '';

    public string $measured_at = '';

    public function boot(BodyWeightRepositoryInterface $bodyWeightRepository): void
    {
        $this->bodyWeightRepository = $bodyWeightRepository;
    }

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->measured_at = now()->toDateString();
    }

    /**
     * Log a new body weight entry.
     */
    public function save(): void
    {
        $validated = $this->validate([
            'weight' => ['required', 'numeric', 'min:1', 'max:500'],
            'measured_at' => [
                'required',
                'date',
                'before_or_equal:today',
                Rule::unique('body_weights', 'measured_at')->where('user_id', Auth::id()),
            ],
        ]);

        $this->bodyWeightRepository->create(Auth::user(), $validated);

        Flux::toast(variant: 'success', text: __('Body weight logged.'));

        $this->redirectRoute('body-weights.index', navigate: true);
    }
}
