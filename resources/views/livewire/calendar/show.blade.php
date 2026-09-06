<section class="w-full max-w-2xl">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <flux:button variant="ghost" icon="arrow-left" :href="route('calendar.index')" wire:navigate />

            <div>
                <flux:heading size="xl">{{ $this->dayLabel }}</flux:heading>
            </div>
        </div>

        <div class="flex items-center gap-1">
            <flux:button variant="ghost" size="sm" icon="chevron-left" wire:click="previousDay" />
            <flux:button variant="ghost" size="sm" icon="chevron-right" wire:click="nextDay" />
        </div>
    </div>

    <div class="mt-6 space-y-6">
        <div>
            <flux:heading size="lg">{{ __('Workouts') }}</flux:heading>

            <div class="mt-2 space-y-2">
                @forelse ($this->workouts as $workout)
                    <flux:card wire:key="workout-{{ $workout->id }}" class="flex items-center justify-between gap-2 py-3">
                        <flux:text class="font-medium text-zinc-900 dark:text-white">{{ $workout->name }}</flux:text>

                        <flux:button variant="ghost" size="sm" icon="pencil" :href="route('workouts.edit', $workout)" wire:navigate />
                    </flux:card>
                @empty
                    <div class="p-6 text-center border rounded-lg border-zinc-200 dark:border-zinc-700">
                        <flux:text>{{ __('No workouts logged on this day') }}</flux:text>
                    </div>
                @endforelse
            </div>
        </div>

        <div>
            <flux:heading size="lg">{{ __('Body weight') }}</flux:heading>

            <div class="mt-2">
                @if ($this->bodyWeight)
                    <flux:card class="flex items-center justify-between gap-2 py-3">
                        <flux:text class="font-medium text-zinc-900 dark:text-white">{{ number_format((float) $this->bodyWeight->weight, 2) }} kg</flux:text>

                        <flux:button variant="ghost" size="sm" icon="pencil" :href="route('body-weights.edit', $this->bodyWeight)" wire:navigate />
                    </flux:card>
                @else
                    <div class="flex items-center justify-between gap-2 p-6 border rounded-lg border-zinc-200 dark:border-zinc-700">
                        <flux:text>{{ __('No body weight logged on this day') }}</flux:text>

                        <flux:button variant="ghost" size="sm" icon="plus" :href="route('body-weights.create')" wire:navigate>
                            {{ __('Log weight') }}
                        </flux:button>
                    </div>
                @endif
            </div>
        </div>

        <div>
            <flux:heading size="lg">{{ __('Sleep') }}</flux:heading>

            <div class="mt-2 space-y-2">
                @forelse ($this->sleeps as $sleep)
                    <flux:card wire:key="sleep-{{ $sleep->id }}" class="flex items-center justify-between gap-2 py-3">
                        <div>
                            <flux:text class="font-medium text-zinc-900 dark:text-white">
                                {{ __(':hours h :minutes m', ['hours' => intdiv($sleep->durationInMinutes(), 60), 'minutes' => $sleep->durationInMinutes() % 60]) }}
                            </flux:text>
                            <flux:text size="sm">
                                {{ $sleep->started_at->translatedFormat('Y. F j. H:i') }} &ndash; {{ $sleep->ended_at->translatedFormat('H:i') }}
                            </flux:text>
                        </div>

                        <flux:button variant="ghost" size="sm" icon="pencil" :href="route('sleeps.edit', $sleep)" wire:navigate />
                    </flux:card>
                @empty
                    <div class="flex items-center justify-between gap-2 p-6 border rounded-lg border-zinc-200 dark:border-zinc-700">
                        <flux:text>{{ __('No sleep logged on this day') }}</flux:text>

                        <flux:button variant="ghost" size="sm" icon="plus" :href="route('sleeps.create')" wire:navigate>
                            {{ __('Log sleep') }}
                        </flux:button>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</section>
