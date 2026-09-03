<section class="w-full max-w-2xl">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Body weight') }}</flux:heading>
            <flux:subheading>{{ __('Track your body weight over time') }}</flux:subheading>
        </div>

        <flux:button variant="primary" icon="plus" :href="route('body-weights.create')" wire:navigate>
            {{ __('Log weight') }}
        </flux:button>
    </div>

    <div class="mt-6 space-y-2">
        @forelse ($this->bodyWeights as $bodyWeight)
            <flux:card wire:key="body-weight-{{ $bodyWeight->id }}" class="flex items-center justify-between gap-2 py-3">
                <div>
                    <flux:text class="font-medium text-zinc-900 dark:text-white">{{ number_format((float) $bodyWeight->weight, 2) }} kg</flux:text>
                    <flux:text size="sm">{{ $bodyWeight->measured_at->translatedFormat('Y. F j.') }}</flux:text>
                </div>

                <div class="flex items-center gap-1">
                    <flux:button
                        variant="ghost"
                        size="sm"
                        icon="pencil"
                        :href="route('body-weights.edit', $bodyWeight)"
                        wire:navigate
                    />
                    <flux:button
                        variant="ghost"
                        size="sm"
                        icon="trash"
                        wire:click="delete({{ $bodyWeight->id }})"
                        wire:confirm="{{ __('Delete this body weight entry?') }}"
                    />
                </div>
            </flux:card>
        @empty
            <div class="p-8 text-center border rounded-lg border-zinc-200 dark:border-zinc-700">
                <p class="font-medium">{{ __('No body weight entries yet') }}</p>
                <flux:text class="mt-1">{{ __('Log your first weight to start tracking your progress') }}</flux:text>
            </div>
        @endforelse
    </div>
</section>
