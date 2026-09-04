<section class="w-full">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <flux:heading size="xl">{{ __('Users') }}</flux:heading>
            <flux:subheading>{{ __('Find and follow other users') }}</flux:subheading>
        </div>

        <flux:button variant="ghost" icon="user-group" :href="route('users.following')" wire:navigate>
            {{ __('Following') }}
        </flux:button>
    </div>

    <flux:input
        wire:model.live.debounce.300ms="search"
        icon="magnifying-glass"
        :placeholder="__('Search by name or email')"
        class="mt-6 max-w-md"
    />

    <div class="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($this->users as $user)
            <flux:card wire:key="user-{{ $user->id }}" class="flex items-center gap-3">
                <a href="{{ route('users.show', $user) }}" wire:navigate class="flex min-w-0 flex-1 items-center gap-3">
                    <flux:avatar :src="$user->avatar_url" :name="$user->name" :initials="$user->initials()" />

                    <div class="min-w-0 flex-1">
                        <flux:heading class="truncate">{{ $user->name }}</flux:heading>
                        <flux:text class="truncate">{{ $user->email }}</flux:text>
                    </div>
                </a>

                @if (in_array($user->id, $this->followingIds, true))
                    <flux:button size="sm" variant="ghost" wire:click="unfollow({{ $user->id }})">
                        {{ __('Unfollow') }}
                    </flux:button>
                @else
                    <flux:button size="sm" variant="primary" wire:click="follow({{ $user->id }})">
                        {{ __('Follow') }}
                    </flux:button>
                @endif
            </flux:card>
        @empty
            <div class="col-span-full p-8 text-center border rounded-lg border-zinc-200 dark:border-zinc-700">
                <p class="font-medium">{{ __('No users found') }}</p>
                <flux:text class="mt-1">{{ __('Try a different search term') }}</flux:text>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        <flux:pagination :paginator="$this->users" />
    </div>
</section>
