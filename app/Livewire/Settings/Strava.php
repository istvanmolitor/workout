<?php

namespace App\Livewire\Settings;

use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Strava')]
class Strava extends Component
{
    /**
     * Mount the component.
     */
    public function mount(): void
    {
        if (session('status') === 'strava-connected') {
            Flux::toast(variant: 'success', text: __('Strava account connected.'));
        }
    }

    /**
     * Disconnect the Strava account from the current user.
     */
    public function disconnect(): void
    {
        $user = Auth::user();

        $user->strava_id = null;
        $user->strava_token = null;
        $user->strava_refresh_token = null;
        $user->strava_token_expires_at = null;
        $user->save();

        Flux::toast(variant: 'success', text: __('Strava account disconnected.'));
    }

    #[Computed]
    public function connected(): bool
    {
        return Auth::user()->hasStravaConnected();
    }
}
