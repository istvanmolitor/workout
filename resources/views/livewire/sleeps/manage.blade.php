<section class="w-full max-w-2xl">
    <flux:button variant="ghost" icon="arrow-left" class="mb-4 lg:hidden" :href="route('dashboard')" wire:navigate>
        {{ __('Dashboard') }}
    </flux:button>

    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Sleep') }}</flux:heading>
            <flux:subheading>{{ __('Track your sleep over time') }}</flux:subheading>
        </div>

        <flux:button variant="primary" icon="plus" :href="route('sleeps.create')" wire:navigate>
            {{ __('Log sleep') }}
        </flux:button>
    </div>

    <div class="mt-6 space-y-2">
        @forelse ($this->sleeps as $sleep)
            <flux:card wire:key="sleep-{{ $sleep->id }}" class="flex items-center justify-between gap-2 py-3">
                <div>
                    <flux:text class="font-medium text-zinc-900 dark:text-white">
                        {{ __(':hours h :minutes m', ['hours' => intdiv($sleep->durationInMinutes(), 60), 'minutes' => $sleep->durationInMinutes() % 60]) }}
                    </flux:text>
                    <flux:text size="sm">
                        {{ $sleep->started_at->translatedFormat('Y. F j. H:i') }} &ndash; {{ $sleep->ended_at->translatedFormat('H:i') }}
                    </flux:text>
                    @if ($sleep->quality)
                        <flux:text size="sm">{{ __('Quality') }}: {{ $sleep->quality }}/5</flux:text>
                    @endif
                </div>

                <div class="flex items-center gap-1">
                    <flux:button
                        variant="ghost"
                        size="sm"
                        icon="pencil"
                        :href="route('sleeps.edit', $sleep)"
                        wire:navigate
                    />
                    <flux:button
                        variant="ghost"
                        size="sm"
                        icon="trash"
                        wire:click="delete({{ $sleep->id }})"
                        wire:confirm="{{ __('Delete this sleep entry?') }}"
                    />
                </div>
            </flux:card>
        @empty
            <div class="p-8 text-center border rounded-lg border-zinc-200 dark:border-zinc-700">
                <p class="font-medium">{{ __('No sleep entries yet') }}</p>
                <flux:text class="mt-1">{{ __('Log your first night to start tracking your sleep') }}</flux:text>
            </div>
        @endforelse
    </div>
</section>
