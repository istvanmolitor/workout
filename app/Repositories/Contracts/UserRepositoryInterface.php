<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{
    /**
     * Search for users by name or email, excluding the given user.
     *
     * @return LengthAwarePaginator<int, User>
     */
    public function search(int|string|null $excludeUserId, string $search, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get the users the given user follows, ordered by name.
     *
     * @return Collection<int, User>
     */
    public function following(User $user): Collection;

    /**
     * Get the ids of the users the given user follows.
     *
     * @return array<int, int>
     */
    public function followingIds(User $user): array;

    /**
     * Make the user follow the target user.
     */
    public function follow(User $user, User $target): void;

    /**
     * Make the user unfollow the target user.
     */
    public function unfollow(User $user, User $target): void;
}
