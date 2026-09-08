<div class="space-y-6">
    <div class="flex items-end gap-3">
        <flux:input wire:model="barcode" :label="__('Barcode (optional)')" class="flex-1" />

        <flux:button type="button" wire:click="fetchNutrition" icon="magnifying-glass">
            {{ __('Fetch nutrition data') }}
        </flux:button>
    </div>

    @if ($nutritionSynced)
        <flux:badge color="lime" size="sm" icon="check">
            {{ __('Nutrition values filled in from the free food database') }}
        </flux:badge>
    @endif

    @if ($this->nutrients->isNotEmpty())
        <flux:separator :text="__('Nutrition values (per 100g)')" />

        <div class="grid gap-4 sm:grid-cols-2">
            @foreach ($this->nutrients as $nutrient)
                <flux:input
                    wire:model="nutrientValues.{{ $nutrient->slug }}"
                    type="number"
                    step="any"
                    min="0"
                    :label="$nutrient->name.' ('.$nutrient->unit.')'"
                />
            @endforeach
        </div>
    @endif
</div>
