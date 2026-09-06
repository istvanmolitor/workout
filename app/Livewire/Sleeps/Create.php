<?php

namespace App\Livewire\Sleeps;

use App\Repositories\Contracts\SleepRepositoryInterface;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Új alvás')]
class Create extends Component
{
    protected SleepRepositoryInterface $sleepRepository;

    public string $started_at = '';

    public string $ended_at = '';

    public string $quality = '';

    public string $notes = '';

    public function boot(SleepRepositoryInterface $sleepRepository): void
    {
        $this->sleepRepository = $sleepRepository;
    }

    /**
     * Log a new sleep entry.
     */
    public function save(): void
    {
        $validated = $this->validate([
            'started_at' => [
                'required',
                'date',
                'before_or_equal:now',
                Rule::unique('sleeps', 'started_at')->where('user_id', Auth::id()),
            ],
            'ended_at' => ['required', 'date', 'after:started_at'],
            'quality' => ['nullable', 'integer', 'between:1,5'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->sleepRepository->create(Auth::user(), $validated);

        Flux::toast(variant: 'success', text: __('Sleep logged.'));

        $this->redirectRoute('sleeps.index', navigate: true);
    }
}
