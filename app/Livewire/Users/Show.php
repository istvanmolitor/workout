<?php

namespace App\Livewire\Users;

use App\Models\User;
use App\Models\Workout;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Felhasználói profil')]
class Show extends Component
{
    use WithPagination;

    #[Locked]
    public User $user;

    /**
     * Mount the component.
     */
    public function mount(User $user): void
    {
        $this->user = $user;
    }

    /**
     * Get the logged workouts of the profile's user.
     *
     * @return LengthAwarePaginator<int, Workout>
     */
    #[Computed]
    public function workouts(): LengthAwarePaginator
    {
        return $this->user->workouts()
            ->with('exercises.exercise', 'exercises.sets.values.field')
            ->latest('performed_at')
            ->latest()
            ->paginate(10);
    }

    /**
     * Determine whether the authenticated user is following the profile's user.
     */
    #[Computed]
    public function isFollowing(): bool
    {
        return Auth::user()->isFollowing($this->user);
    }

    /**
     * Follow the profile's user.
     */
    public function follow(): void
    {
        Auth::user()->following()->syncWithoutDetaching([$this->user->id]);

        unset($this->isFollowing);

        Flux::toast(variant: 'success', text: __('You are now following :name.', ['name' => $this->user->name]));
    }

    /**
     * Unfollow the profile's user.
     */
    public function unfollow(): void
    {
        Auth::user()->following()->detach($this->user->id);

        unset($this->isFollowing);

        Flux::toast(text: __('You unfollowed :name.', ['name' => $this->user->name]));
    }
}
