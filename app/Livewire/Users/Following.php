<?php

namespace App\Livewire\Users;

use App\Models\User;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Követettek')]
class Following extends Component
{
    /**
     * Get the users the authenticated user is following.
     *
     * @return Collection<int, User>
     */
    #[Computed]
    public function following(): Collection
    {
        return Auth::user()->following()->orderBy('name')->get();
    }

    /**
     * Unfollow the given user.
     */
    public function unfollow(User $user): void
    {
        Auth::user()->following()->detach($user->id);

        unset($this->following);

        Flux::toast(text: __('You unfollowed :name.', ['name' => $user->name]));
    }
}
