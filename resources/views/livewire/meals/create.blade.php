<section class="w-full max-w-lg">
    <div class="flex items-center gap-3">
        <flux:button variant="ghost" icon="arrow-left" :href="route('meals.index')" wire:navigate />

        <div>
            <flux:heading size="xl">{{ __('Log meal') }}</flux:heading>
            <flux:subheading>{{ __('Record what you ate') }}</flux:subheading>
        </div>
    </div>

    <form wire:submit="save" class="mt-6 space-y-6">
        <x-meals.food-picker :selected-foods="$this->selectedFoods" :food-search="$foodSearch" :food-results="$this->foodResults" />

        <flux:input wire:model="eaten_at" type="datetime-local" :label="__('Eaten at')" required />

        <flux:select wire:model="type" :label="__('Type')" :placeholder="__('Not set')">
            <flux:select.option value="breakfast">{{ __('Breakfast') }}</flux:select.option>
            <flux:select.option value="lunch">{{ __('Lunch') }}</flux:select.option>
            <flux:select.option value="dinner">{{ __('Dinner') }}</flux:select.option>
            <flux:select.option value="snack">{{ __('Snack') }}</flux:select.option>
        </flux:select>

        <flux:textarea wire:model="notes" :label="__('Notes')" rows="3" />

        <div class="flex items-center gap-4">
            <flux:button type="submit" variant="primary">
                {{ __('Save entry') }}
            </flux:button>
        </div>
    </form>
</section>
