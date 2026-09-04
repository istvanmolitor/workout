<?php

namespace App\Livewire\Users;

use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Felhasználók')]
class Search extends Component
{
    use WithPagination;

    public string $search = '';

    /**
     * Get the users matching the current search term, excluding the authenticated user.
     *
     * @return LengthAwarePaginator<int, User>
     */
    #[Computed]
    public function users(): LengthAwarePaginator
    {
        return User::query()
            ->where('id', '!=', Auth::id())
            ->when($this->search !== '', fn ($query) => $query->where(
                fn ($query) => $query->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")
            ))
            ->orderBy('name')
            ->paginate(15);
    }

    /**
     * Get the ids of the users the authenticated user is following.
     *
     * @return array<int, int>
     */
    #[Computed]
    public function followingIds(): array
    {
        return Auth::user()->following()->pluck('users.id')->all();
    }

    /**
     * Reset pagination when the search term changes.
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Follow the given user.
     */
    public function follow(User $user): void
    {
        Auth::user()->following()->syncWithoutDetaching([$user->id]);

        unset($this->followingIds);

        Flux::toast(variant: 'success', text: __('You are now following :name.', ['name' => $user->name]));
    }

    /**
     * Unfollow the given user.
     */
    public function unfollow(User $user): void
    {
        Auth::user()->following()->detach($user->id);

        unset($this->followingIds);

        Flux::toast(text: __('You unfollowed :name.', ['name' => $user->name]));
    }
}
