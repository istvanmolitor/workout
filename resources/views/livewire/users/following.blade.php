<section class="w-full">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <flux:heading size="xl">{{ __('Following') }}</flux:heading>
            <flux:subheading>{{ __('Users you follow') }}</flux:subheading>
        </div>

        <flux:button variant="ghost" icon="magnifying-glass" :href="route('users.index')" wire:navigate>
            {{ __('Find users') }}
        </flux:button>
    </div>

    <div class="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($this->following as $user)
            <flux:card wire:key="following-{{ $user->id }}" class="flex items-center gap-3">
                <flux:avatar :src="$user->avatar_url" :name="$user->name" :initials="$user->initials()" />

                <div class="min-w-0 flex-1">
                    <flux:heading class="truncate">{{ $user->name }}</flux:heading>
                    <flux:text class="truncate">{{ $user->email }}</flux:text>
                </div>

                <flux:button size="sm" variant="ghost" wire:click="unfollow({{ $user->id }})">
                    {{ __('Unfollow') }}
                </flux:button>
            </flux:card>
        @empty
            <div class="col-span-full p-8 text-center border rounded-lg border-zinc-200 dark:border-zinc-700">
                <p class="font-medium">{{ __('Not following anyone yet') }}</p>
                <flux:text class="mt-1">{{ __('Search for users to start following them') }}</flux:text>

                <flux:button class="mt-4" variant="primary" :href="route('users.index')" wire:navigate>
                    {{ __('Find users') }}
                </flux:button>
            </div>
        @endforelse
    </div>
</section>
