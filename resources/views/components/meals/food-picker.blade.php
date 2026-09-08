@props(['selectedFoods', 'foodSearch', 'foodResults'])

<div>
    <flux:label>{{ __('Foods') }}</flux:label>

    @if ($selectedFoods->isNotEmpty())
        <div class="mt-1 space-y-2">
            @foreach ($selectedFoods as $food)
                <flux:card wire:key="selected-food-{{ $food->id }}" class="flex items-center justify-between gap-2 py-3">
                    <div>
                        <flux:text class="font-medium text-zinc-900 dark:text-white">{{ $food->name }}</flux:text>
                        @if ($food->calories)
                            <flux:text size="sm">{{ __(':calories kcal', ['calories' => $food->calories]) }}</flux:text>
                        @endif
                    </div>

                    <div class="flex items-start gap-2">
                        <div class="w-28">
                            <flux:input
                                wire:model="foodQuantities.{{ $food->id }}"
                                type="number"
                                step="any"
                                min="0"
                                :placeholder="__('Amount (g)')"
                                required
                            />

                            <flux:error name="foodQuantities.{{ $food->id }}" />
                        </div>

                        <flux:button type="button" variant="ghost" size="sm" icon="x-mark" wire:click="removeFood({{ $food->id }})" />
                    </div>
                </flux:card>
            @endforeach
        </div>
    @endif

    <flux:input wire:model.live.debounce.300ms="foodSearch" class="mt-2" :placeholder="__('Start typing to search')" autocomplete="off" autofocus />

    @if (trim($foodSearch) !== '')
        <div class="mt-2 divide-y divide-zinc-200 rounded-lg border border-zinc-200 dark:divide-zinc-700 dark:border-zinc-700">
            @foreach ($foodResults as $food)
                <button
                    type="button"
                    wire:key="food-result-{{ $food->id }}"
                    wire:click="addFood({{ $food->id }})"
                    class="flex w-full items-center justify-between gap-2 px-3 py-2 text-left hover:bg-zinc-100 dark:hover:bg-zinc-700"
                >
                    <flux:text class="font-medium text-zinc-900 dark:text-white">{{ $food->name }}</flux:text>
                    @if ($food->calories)
                        <flux:text size="sm">{{ __(':calories kcal', ['calories' => $food->calories]) }}</flux:text>
                    @endif
                </button>
            @endforeach

            <div class="flex items-end gap-2 px-3 py-2">
                <flux:input wire:model="newFoodCalories" type="number" min="0" :label="__('Calories (optional)')" class="w-32" />

                <flux:button type="button" variant="outline" wire:click="createFood">
                    {{ __('Add “:name” as a new food', ['name' => trim($foodSearch)]) }}
                </flux:button>
            </div>
        </div>
    @endif

    <flux:error name="foodIds" />
</div>
