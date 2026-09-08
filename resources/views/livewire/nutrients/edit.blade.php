<section class="w-full max-w-lg">
    <div class="flex items-center gap-3">
        <flux:button variant="ghost" icon="arrow-left" :href="route('nutrients.index')" wire:navigate />

        <div>
            <flux:heading size="xl">{{ __('Edit nutrient') }}</flux:heading>
            <flux:subheading>{{ __('Update the nutrient details') }}</flux:subheading>
        </div>
    </div>

    <form wire:submit="save" class="mt-6 space-y-6">
        <flux:input wire:model="name" :label="__('Name')" required autofocus />

        <flux:input wire:model="slug" :label="__('Slug')" required />

        <flux:input wire:model="unit" :label="__('Unit')" required />

        <flux:input wire:model="decimal_places" type="number" min="0" max="6" :label="__('Decimal places')" required />

        <flux:input wire:model="sort_order" type="number" min="0" :label="__('Sort order')" required />

        <div class="flex items-center gap-4">
            <flux:button type="submit" variant="primary">
                {{ __('Save changes') }}
            </flux:button>
        </div>
    </form>
</section>
