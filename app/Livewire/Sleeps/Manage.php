<?php

namespace App\Livewire\Sleeps;

use App\Models\Sleep;
use App\Repositories\Contracts\SleepRepositoryInterface;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Alvás')]
class Manage extends Component
{
    protected SleepRepositoryInterface $sleepRepository;

    public function boot(SleepRepositoryInterface $sleepRepository): void
    {
        $this->sleepRepository = $sleepRepository;
    }

    /**
     * Get the authenticated user's sleep entries.
     *
     * @return Collection<int, Sleep>
     */
    #[Computed]
    public function sleeps(): Collection
    {
        return $this->sleepRepository->forUser(Auth::user());
    }

    /**
     * Delete a sleep entry.
     */
    public function delete(Sleep $sleep): void
    {
        $this->authorize('delete', $sleep);

        $this->sleepRepository->delete($sleep);

        unset($this->sleeps);

        Flux::toast(variant: 'success', text: __('Sleep entry deleted.'));
    }
}
