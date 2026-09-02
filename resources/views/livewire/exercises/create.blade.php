<section class="w-full max-w-lg">
    <div class="flex items-center gap-3">
        <flux:button variant="ghost" icon="arrow-left" :href="route('exercises.index')" wire:navigate />

        <div>
            <flux:heading size="xl">{{ __('New exercise') }}</flux:heading>
            <flux:subheading>{{ __('Add an exercise to your catalog') }}</flux:subheading>
        </div>
    </div>

    <form wire:submit="save" class="mt-6 space-y-6">
        <flux:input wire:model="name" :label="__('Name')" :placeholder="__('e.g. Bench press')" required autofocus />

        <div class="flex items-center gap-4">
            <flux:button type="submit" variant="primary">
                {{ __('Create exercise') }}
            </flux:button>
        </div>
    </form>
</section>
