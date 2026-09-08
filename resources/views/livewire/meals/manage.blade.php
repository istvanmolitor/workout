<section class="w-full max-w-2xl">
    <flux:button variant="ghost" icon="arrow-left" class="mb-4 lg:hidden" :href="route('dashboard')" wire:navigate>
        {{ __('Dashboard') }}
    </flux:button>

    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Meals') }}</flux:heading>
            <flux:subheading>{{ __('Track what you eat over time') }}</flux:subheading>
        </div>

        <div class="flex items-center gap-2">
            @if (auth()->user()->isAdmin())
                <flux:button variant="ghost" icon="cake" :href="route('foods.index')" wire:navigate>
                    {{ __('Foods') }}
                </flux:button>
            @endif

            <flux:button variant="ghost" icon="chart-bar" :href="route('meals.daily')" wire:navigate>
                {{ __('Daily summary') }}
            </flux:button>

            <flux:button variant="primary" icon="plus" :href="route('meals.create')" wire:navigate>
                {{ __('Log meal') }}
            </flux:button>
        </div>
    </div>

    <div class="mt-6 space-y-2">
        @forelse ($this->meals as $meal)
            <flux:card wire:key="meal-{{ $meal->id }}" class="flex items-center justify-between gap-2 py-3">
                <div>
                    <flux:text class="font-medium text-zinc-900 dark:text-white">
                        {{ $meal->foods->pluck('name')->join(', ') }}
                    </flux:text>
                    <flux:text size="sm">
                        {{ $meal->eaten_at->translatedFormat('Y. F j. H:i') }}
                        @if ($meal->type)
                            &middot; {{ __(ucfirst($meal->type)) }}
                        @endif
                    </flux:text>
                    @if ($meal->foods->sum('calories'))
                        <flux:text size="sm">{{ __(':calories kcal', ['calories' => $meal->foods->sum('calories')]) }}</flux:text>
                    @endif
                </div>

                <div class="flex items-center gap-1">
                    <flux:button
                        variant="ghost"
                        size="sm"
                        icon="pencil"
                        :href="route('meals.edit', $meal)"
                        wire:navigate
                    />
                    <flux:button
                        variant="ghost"
                        size="sm"
                        icon="trash"
                        wire:click="delete({{ $meal->id }})"
                        wire:confirm="{{ __('Delete this meal entry?') }}"
                    />
                </div>
            </flux:card>
        @empty
            <div class="p-8 text-center border rounded-lg border-zinc-200 dark:border-zinc-700">
                <p class="font-medium">{{ __('No meals logged yet') }}</p>
                <flux:text class="mt-1">{{ __('Log your first meal to start tracking what you eat') }}</flux:text>
            </div>
        @endforelse
    </div>
</section>
