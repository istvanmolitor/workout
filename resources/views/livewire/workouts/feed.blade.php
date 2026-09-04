<section class="w-full">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <flux:heading size="xl">{{ __('Followed workouts') }}</flux:heading>
            <flux:subheading>{{ __('See what the users you follow have been up to') }}</flux:subheading>
        </div>

        <flux:button variant="ghost" icon="user-group" :href="route('users.following')" wire:navigate>
            {{ __('Following') }}
        </flux:button>
    </div>

    <div class="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($this->workouts as $workout)
            <flux:card wire:key="feed-workout-{{ $workout->id }}" class="space-y-3">
                <div class="flex items-center gap-2">
                    <flux:avatar
                        size="sm"
                        :src="$workout->user->avatar_url"
                        :name="$workout->user->name"
                        :initials="$workout->user->initials()"
                    />
                    <flux:text class="truncate">{{ $workout->user->name }}</flux:text>
                </div>

                <div>
                    <flux:heading>{{ $workout->name }}</flux:heading>
                    <flux:text class="mt-1">{{ $workout->performed_at->translatedFormat('Y. m. d.') }}</flux:text>
                </div>

                <flux:separator />

                <ul class="space-y-1">
                    @foreach ($workout->exercises as $exercise)
                        <li class="flex items-center justify-between text-sm">
                            <span>{{ $exercise->exercise->name }}</span>
                            @if ($exercise->sets->contains(fn ($set) => $set->values->contains(fn ($value) => $value->completed_value !== null)))
                                <flux:badge size="sm">
                                    {{ $exercise->sets->map(fn ($set) => $set->values->map(fn ($value) => rtrim(rtrim($value->completed_value ?? '?', '0'), '.').($value->field->unit ? ' '.$value->field->unit : ''))->join('×'))->join(', ') }}
                                    @if ($exercise->difficulty !== null)
                                        &middot; {{ $exercise->difficulty }}/5
                                    @endif
                                </flux:badge>
                            @else
                                <flux:badge size="sm">{{ __('Not logged yet') }}</flux:badge>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </flux:card>
        @empty
            <div class="col-span-full p-8 text-center border rounded-lg border-zinc-200 dark:border-zinc-700">
                <p class="font-medium">{{ __('Nothing to show yet') }}</p>
                <flux:text class="mt-1">{{ __('Follow some users to see their workouts here') }}</flux:text>

                <flux:button class="mt-4" variant="primary" :href="route('users.index')" wire:navigate>
                    {{ __('Find users') }}
                </flux:button>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        <flux:pagination :paginator="$this->workouts" />
    </div>
</section>
