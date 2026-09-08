<section class="w-full max-w-2xl">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Foods') }}</flux:heading>
            <flux:subheading>{{ __('Manage the food catalog used when logging meals') }}</flux:subheading>
        </div>

        <flux:button variant="primary" icon="plus" :href="route('foods.create')" wire:navigate>
            {{ __('New food') }}
        </flux:button>
    </div>

    <flux:input
        wire:model.live.debounce.300ms="search"
        icon="magnifying-glass"
        :placeholder="__('Search by name')"
        class="mt-6 max-w-md"
    />

    <div class="mt-6 space-y-2">
        @forelse ($this->foods as $food)
            <flux:card wire:key="food-{{ $food->id }}" class="flex items-center justify-between gap-2 py-3">
                <div>
                    <flux:text class="font-medium text-zinc-900 dark:text-white">{{ $food->name }}</flux:text>
                    @if ($food->calories)
                        <flux:text size="sm">{{ __(':calories kcal', ['calories' => $food->calories]) }}</flux:text>
                    @endif
                </div>

                <div class="flex items-center gap-1">
                    <flux:button
                        variant="ghost"
                        size="sm"
                        icon="pencil"
                        :href="route('foods.edit', $food)"
                        wire:navigate
                    />
                    <flux:button
                        variant="ghost"
                        size="sm"
                        icon="trash"
                        wire:click="delete({{ $food->id }})"
                        wire:confirm="{{ __('Delete this food?') }}"
                    />
                </div>
            </flux:card>
        @empty
            <div class="p-8 text-center border rounded-lg border-zinc-200 dark:border-zinc-700">
                @if ($search !== '')
                    <p class="font-medium">{{ __('No foods found') }}</p>
                    <flux:text class="mt-1">{{ __('Try a different search term') }}</flux:text>
                @else
                    <p class="font-medium">{{ __('No foods yet') }}</p>
                    <flux:text class="mt-1">{{ __('Add your first food to the catalog') }}</flux:text>
                @endif
            </div>
        @endforelse
    </div>
</section>
