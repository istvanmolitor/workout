<section class="w-full">
    <div>
        <flux:heading size="xl">{{ __('Workouts') }}</flux:heading>
        <flux:subheading>{{ __('Review your logged workouts') }}</flux:subheading>
    </div>

    <div class="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($this->workouts as $workout)
            <flux:card wire:key="workout-{{ $workout->id }}" class="space-y-3">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <flux:heading>{{ $workout->name }}</flux:heading>
                        <flux:text class="mt-1">{{ $workout->performed_at->translatedFormat('Y. m. d.') }}</flux:text>
                    </div>

                    <div class="flex items-center gap-1">
                        <flux:button
                            variant="ghost"
                            size="sm"
                            icon="play"
                            :href="route('workouts.perform', $workout)"
                            wire:navigate
                        />

                        <flux:button
                            variant="ghost"
                            size="sm"
                            icon="pencil"
                            :href="route('workouts.edit', $workout)"
                            wire:navigate
                        />
                    </div>
                </div>

                <flux:separator />

                <ul class="space-y-1">
                    @foreach ($workout->exercises as $exercise)
                        <li class="flex items-center justify-between text-sm">
                            <span>{{ $exercise->exercise->name }}</span>
                            @if ($exercise->sets->contains(fn ($set) => $set->completed_reps !== null))
                                <flux:badge size="sm">
                                    {{ $exercise->sets->map(fn ($set) => ($set->completed_reps ?? '?').($set->completed_weight !== null ? '×'.rtrim(rtrim($set->completed_weight, '0'), '.').'kg' : ''))->join(', ') }}
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
                <p class="font-medium">{{ __('No workouts yet') }}</p>
                <flux:text class="mt-1">{{ __('Start a workout from one of your workout plans to get going') }}</flux:text>
            </div>
        @endforelse
    </div>
</section>
