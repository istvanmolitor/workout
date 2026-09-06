<section class="w-full max-w-lg">
    <div class="flex items-center gap-3">
        <flux:button variant="ghost" icon="arrow-left" :href="route('sleeps.index')" wire:navigate />

        <div>
            <flux:heading size="xl">{{ __('Edit sleep entry') }}</flux:heading>
            <flux:subheading>{{ __('Update this sleep entry') }}</flux:subheading>
        </div>
    </div>

    <form wire:submit="save" class="mt-6 space-y-6">
        <flux:input wire:model="started_at" type="datetime-local" :label="__('Went to bed')" required autofocus />

        <flux:input wire:model="ended_at" type="datetime-local" :label="__('Woke up')" required />

        <flux:select wire:model="quality" :label="__('Sleep quality')" :placeholder="__('Not rated')">
            <flux:select.option value="1">{{ __('Very poor') }}</flux:select.option>
            <flux:select.option value="2">{{ __('Poor') }}</flux:select.option>
            <flux:select.option value="3">{{ __('Fair') }}</flux:select.option>
            <flux:select.option value="4">{{ __('Good') }}</flux:select.option>
            <flux:select.option value="5">{{ __('Excellent') }}</flux:select.option>
        </flux:select>

        <flux:textarea wire:model="notes" :label="__('Notes')" rows="3" />

        <div class="flex items-center gap-4">
            <flux:button type="submit" variant="primary">
                {{ __('Save changes') }}
            </flux:button>
        </div>
    </form>
</section>
