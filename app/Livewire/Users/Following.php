<?php

namespace App\Livewire\Users;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Követettek')]
class Following extends Component
{
    protected UserRepositoryInterface $userRepository;

    public function boot(UserRepositoryInterface $userRepository): void
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Get the users the authenticated user is following.
     *
     * @return Collection<int, User>
     */
    #[Computed]
    public function following(): Collection
    {
        return $this->userRepository->following(Auth::user());
    }

    /**
     * Unfollow the given user.
     */
    public function unfollow(User $user): void
    {
        $this->userRepository->unfollow(Auth::user(), $user);

        unset($this->following);

        Flux::toast(text: __('You unfollowed :name.', ['name' => $user->name]));
    }
}
