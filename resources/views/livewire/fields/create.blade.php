<section class="w-full max-w-lg">
    <div class="flex items-center gap-3">
        <flux:button variant="ghost" icon="arrow-left" :href="route('fields.index')" wire:navigate />

        <div>
            <flux:heading size="xl">{{ __('New field') }}</flux:heading>
            <flux:subheading>{{ __('Add a trackable field for exercise types') }}</flux:subheading>
        </div>
    </div>

    <form wire:submit="save" class="mt-6 space-y-6">
        <flux:input wire:model="name" :label="__('Name')" :placeholder="__('e.g. Weight')" required autofocus />

        <flux:input wire:model="unit" :label="__('Unit')" :placeholder="__('e.g. kg')" />

        <div class="flex items-center gap-4">
            <flux:button type="submit" variant="primary">
                {{ __('Create field') }}
            </flux:button>
        </div>
    </form>
</section>
