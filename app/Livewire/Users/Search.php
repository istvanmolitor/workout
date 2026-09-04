<?php

namespace App\Livewire\Users;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
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

    protected UserRepositoryInterface $userRepository;

    public string $search = '';

    public function boot(UserRepositoryInterface $userRepository): void
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Get the users matching the current search term, excluding the authenticated user.
     *
     * @return LengthAwarePaginator<int, User>
     */
    #[Computed]
    public function users(): LengthAwarePaginator
    {
        return $this->userRepository->search(Auth::id(), $this->search);
    }

    /**
     * Get the ids of the users the authenticated user is following.
     *
     * @return array<int, int>
     */
    #[Computed]
    public function followingIds(): array
    {
        return $this->userRepository->followingIds(Auth::user());
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
        $this->userRepository->follow(Auth::user(), $user);

        unset($this->followingIds);

        Flux::toast(variant: 'success', text: __('You are now following :name.', ['name' => $user->name]));
    }

    /**
     * Unfollow the given user.
     */
    public function unfollow(User $user): void
    {
        $this->userRepository->unfollow(Auth::user(), $user);

        unset($this->followingIds);

        Flux::toast(text: __('You unfollowed :name.', ['name' => $user->name]));
    }
}
