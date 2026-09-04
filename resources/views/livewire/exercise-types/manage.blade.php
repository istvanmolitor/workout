<section class="w-full max-w-2xl">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Exercise types') }}</flux:heading>
            <flux:subheading>{{ __('Manage the types that determine which fields exercises track') }}</flux:subheading>
        </div>

        <flux:button variant="primary" icon="plus" :href="route('exercise-types.create')" wire:navigate>
            {{ __('New exercise type') }}
        </flux:button>
    </div>

    <div class="mt-6 space-y-2">
        @forelse ($this->exerciseTypes as $exerciseType)
            <flux:card wire:key="exercise-type-{{ $exerciseType->id }}" class="space-y-2 py-3">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <flux:text>{{ $exerciseType->name }}</flux:text>
                        @if ($exerciseType->single_set)
                            <flux:badge size="sm" color="zinc">{{ __('Single set') }}</flux:badge>
                        @endif
                    </div>

                    <div class="flex items-center gap-1">
                        <flux:button
                            variant="ghost"
                            size="sm"
                            icon="pencil"
                            :href="route('exercise-types.edit', $exerciseType)"
                            wire:navigate
                        />
                        <flux:button
                            variant="ghost"
                            size="sm"
                            icon="trash"
                            wire:click="delete({{ $exerciseType->id }})"
                            wire:confirm="{{ __('Delete this exercise type?') }}"
                        />
                    </div>
                </div>

                <div class="flex flex-wrap gap-1">
                    @foreach ($exerciseType->fields as $typeField)
                        <flux:badge size="sm">{{ $typeField->field->name }}</flux:badge>
                    @endforeach
                </div>
            </flux:card>
        @empty
            <div class="p-8 text-center border rounded-lg border-zinc-200 dark:border-zinc-700">
                <p class="font-medium">{{ __('No exercise types yet') }}</p>
                <flux:text class="mt-1">{{ __('Add your first exercise type to build your catalog') }}</flux:text>
            </div>
        @endforelse
    </div>
</section>
