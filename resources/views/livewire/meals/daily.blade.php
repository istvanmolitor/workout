<section class="w-full max-w-2xl">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <flux:button variant="ghost" icon="arrow-left" :href="route('meals.index')" wire:navigate />

            <div>
                <flux:heading size="xl">{{ $this->dayLabel }}</flux:heading>
                <flux:subheading>{{ __('Daily summary') }}</flux:subheading>
            </div>
        </div>

        <div class="flex items-center gap-1">
            <flux:button variant="ghost" size="sm" icon="chevron-left" wire:click="previousDay" />
            <flux:button variant="ghost" size="sm" icon="chevron-right" wire:click="nextDay" />
        </div>
    </div>

    <div class="mt-6 overflow-hidden rounded-xl border border-neutral-200 bg-white p-4 dark:border-neutral-700 dark:bg-neutral-800">
        <div class="flex items-baseline gap-2">
            <span class="text-2xl font-semibold text-neutral-900 dark:text-white">{{ $this->totalCalories }}</span>
            <flux:text size="sm">{{ __('kcal today') }}</flux:text>
        </div>

        @if ($this->nutrientTotals->isNotEmpty())
            <flux:separator class="my-4" />

            <dl class="grid grid-cols-2 gap-x-4 gap-y-2 sm:grid-cols-3">
                @foreach ($this->nutrientTotals as $row)
                    <div>
                        <dt class="text-xs text-neutral-500 dark:text-neutral-400">{{ $row['nutrient']->name }}</dt>
                        <dd class="font-medium text-neutral-900 dark:text-white">
                            {{ number_format($row['total'], $row['nutrient']->decimal_places) }} {{ $row['nutrient']->unit }}
                        </dd>
                    </div>
                @endforeach
            </dl>
        @endif
    </div>

    <div class="mt-6 space-y-6">
        @forelse ($this->mealsByType as $type => $mealsOfType)
            <div>
                <flux:heading size="lg">{{ $this->typeLabel($type) }}</flux:heading>

                <div class="mt-2 space-y-2">
                    @foreach ($mealsOfType as $meal)
                        <flux:card wire:key="meal-{{ $meal->id }}" class="flex items-center justify-between gap-2 py-3">
                            <div>
                                <flux:text class="font-medium text-zinc-900 dark:text-white">
                                    {{ $meal->foods->pluck('name')->join(', ') }}
                                </flux:text>
                                <flux:text size="sm">
                                    {{ $meal->eaten_at->translatedFormat('H:i') }}
                                    &middot; {{ __(':calories kcal', ['calories' => $this->mealCalories($meal)]) }}
                                </flux:text>
                            </div>

                            <flux:button variant="ghost" size="sm" icon="pencil" :href="route('meals.edit', $meal)" wire:navigate />
                        </flux:card>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="p-8 text-center border rounded-lg border-zinc-200 dark:border-zinc-700">
                <p class="font-medium">{{ __('No meals logged on this day') }}</p>
                <flux:text class="mt-1">{{ __('Log your first meal to start tracking what you eat') }}</flux:text>

                <flux:button variant="primary" size="sm" icon="plus" class="mt-4" :href="route('meals.create')" wire:navigate>
                    {{ __('Log meal') }}
                </flux:button>
            </div>
        @endforelse
    </div>
</section>
