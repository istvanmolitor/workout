<section class="w-full max-w-lg">
    <div class="flex items-center gap-3">
        <flux:button variant="ghost" icon="arrow-left" :href="route('body-weights.index')" wire:navigate />

        <div>
            <flux:heading size="xl">{{ __('Edit body weight entry') }}</flux:heading>
            <flux:subheading>{{ __('Update this weight entry') }}</flux:subheading>
        </div>
    </div>

    <form wire:submit="save" class="mt-6 space-y-6">
        <flux:input wire:model="weight" type="number" step="0.01" :label="__('Weight (kg)')" required autofocus />

        <flux:input wire:model="measured_at" type="date" :label="__('Date')" required />

        <div class="flex items-center gap-4">
            <flux:button type="submit" variant="primary">
                {{ __('Save changes') }}
            </flux:button>
        </div>
    </form>
</section>
