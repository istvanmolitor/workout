<section class="w-full max-w-2xl">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Nutrients') }}</flux:heading>
            <flux:subheading>{{ __('Manage the nutrient catalog used for food nutrition tracking') }}</flux:subheading>
        </div>

        <flux:button variant="primary" icon="plus" :href="route('nutrients.create')" wire:navigate>
            {{ __('New nutrient') }}
        </flux:button>
    </div>

    <div class="mt-6 space-y-2">
        @forelse ($this->nutrients as $nutrient)
            <flux:card wire:key="nutrient-{{ $nutrient->id }}" class="flex items-center justify-between gap-2 py-3">
                <div class="flex items-center gap-2">
                    <flux:text>{{ $nutrient->name }}</flux:text>
                    <flux:badge size="sm">{{ $nutrient->unit }}</flux:badge>
                    <flux:badge size="sm" variant="subtle">{{ $nutrient->slug }}</flux:badge>
                </div>

                <div class="flex items-center gap-1">
                    <flux:button
                        variant="ghost"
                        size="sm"
                        icon="pencil"
                        :href="route('nutrients.edit', $nutrient)"
                        wire:navigate
                    />
                    <flux:button
                        variant="ghost"
                        size="sm"
                        icon="trash"
                        wire:click="delete({{ $nutrient->id }})"
                        wire:confirm="{{ __('Delete this nutrient?') }}"
                    />
                </div>
            </flux:card>
        @empty
            <div class="p-8 text-center border rounded-lg border-zinc-200 dark:border-zinc-700">
                <p class="font-medium">{{ __('No nutrients yet') }}</p>
                <flux:text class="mt-1">{{ __('Add your first nutrient to build the catalog') }}</flux:text>
            </div>
        @endforelse
    </div>
</section>
