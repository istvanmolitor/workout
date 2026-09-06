<?php

namespace App\Livewire\Sleeps;

use App\Models\Sleep;
use App\Repositories\Contracts\SleepRepositoryInterface;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Alvás szerkesztése')]
class Edit extends Component
{
    protected SleepRepositoryInterface $sleepRepository;

    #[Locked]
    public Sleep $sleep;

    public string $started_at = '';

    public string $ended_at = '';

    public string $quality = '';

    public string $notes = '';

    public function boot(SleepRepositoryInterface $sleepRepository): void
    {
        $this->sleepRepository = $sleepRepository;
    }

    /**
     * Mount the component.
     */
    public function mount(Sleep $sleep): void
    {
        $this->authorize('update', $sleep);

        $this->sleep = $sleep;
        $this->started_at = $sleep->started_at->format('Y-m-d\TH:i');
        $this->ended_at = $sleep->ended_at->format('Y-m-d\TH:i');
        $this->quality = (string) $sleep->quality;
        $this->notes = (string) $sleep->notes;
    }

    /**
     * Update the sleep entry.
     */
    public function save(): void
    {
        $this->authorize('update', $this->sleep);

        $validated = $this->validate([
            'started_at' => [
                'required',
                'date',
                'before_or_equal:now',
                Rule::unique('sleeps', 'started_at')->where('user_id', Auth::id())->ignore($this->sleep->id),
            ],
            'ended_at' => ['required', 'date', 'after:started_at'],
            'quality' => ['nullable', 'integer', 'between:1,5'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->sleepRepository->update($this->sleep, $validated);

        Flux::toast(variant: 'success', text: __('Sleep entry updated.'));

        $this->redirectRoute('sleeps.index', navigate: true);
    }
}
