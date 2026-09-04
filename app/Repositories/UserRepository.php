<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class UserRepository implements UserRepositoryInterface
{
    public function search(int|string|null $excludeUserId, string $search, int $perPage = 15): LengthAwarePaginator
    {
        return User::query()
            ->where('id', '!=', $excludeUserId)
            ->when($search !== '', fn ($query) => $query->where(
                fn ($query) => $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
            ))
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function following(User $user): Collection
    {
        return $user->following()->orderBy('name')->get();
    }

    public function followingIds(User $user): array
    {
        return $user->following()->pluck('users.id')->all();
    }

    public function follow(User $user, User $target): void
    {
        $user->following()->syncWithoutDetaching([$target->id]);
    }

    public function unfollow(User $user, User $target): void
    {
        $user->following()->detach($target->id);
    }
}
