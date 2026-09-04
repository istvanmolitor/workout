<section class="w-full max-w-2xl">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Fields') }}</flux:heading>
            <flux:subheading>{{ __('Manage the trackable fields used by exercise types') }}</flux:subheading>
        </div>

        <flux:button variant="primary" icon="plus" :href="route('fields.create')" wire:navigate>
            {{ __('New field') }}
        </flux:button>
    </div>

    <div class="mt-6 space-y-2">
        @forelse ($this->fields as $field)
            <flux:card wire:key="field-{{ $field->id }}" class="flex items-center justify-between gap-2 py-3">
                <div class="flex items-center gap-2">
                    <flux:text>{{ $field->name }}</flux:text>
                    @if ($field->unit)
                        <flux:badge size="sm">{{ $field->unit }}</flux:badge>
                    @endif
                </div>

                <div class="flex items-center gap-1">
                    <flux:button
                        variant="ghost"
                        size="sm"
                        icon="pencil"
                        :href="route('fields.edit', $field)"
                        wire:navigate
                    />
                    <flux:button
                        variant="ghost"
                        size="sm"
                        icon="trash"
                        wire:click="delete({{ $field->id }})"
                        wire:confirm="{{ __('Delete this field?') }}"
                    />
                </div>
            </flux:card>
        @empty
            <div class="p-8 text-center border rounded-lg border-zinc-200 dark:border-zinc-700">
                <p class="font-medium">{{ __('No fields yet') }}</p>
                <flux:text class="mt-1">{{ __('Add your first field to build your catalog') }}</flux:text>
            </div>
        @endforelse
    </div>
</section>
