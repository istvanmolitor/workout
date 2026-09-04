<section class="w-full max-w-lg">
    <div class="flex items-center gap-3">
        <flux:button variant="ghost" icon="arrow-left" :href="route('fields.index')" wire:navigate />

        <div>
            <flux:heading size="xl">{{ __('Edit field') }}</flux:heading>
            <flux:subheading>{{ __('Update the field name and unit') }}</flux:subheading>
        </div>
    </div>

    <form wire:submit="save" class="mt-6 space-y-6">
        <flux:input wire:model="name" :label="__('Name')" required autofocus />

        <flux:input wire:model="unit" :label="__('Unit')" :placeholder="__('e.g. kg')" />

        <div class="flex items-center gap-4">
            <flux:button type="submit" variant="primary">
                {{ __('Save changes') }}
            </flux:button>
        </div>
    </form>
</section>
