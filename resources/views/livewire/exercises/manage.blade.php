<section class="w-full max-w-2xl">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Exercises') }}</flux:heading>
            <flux:subheading>{{ __('Manage the exercise catalog used in your workout plans') }}</flux:subheading>
        </div>

        <flux:button variant="primary" icon="plus" :href="route('exercises.create')" wire:navigate>
            {{ __('New exercise') }}
        </flux:button>
    </div>

    <div class="mt-6 space-y-2">
        @forelse ($this->exercises as $exercise)
            <flux:card wire:key="exercise-{{ $exercise->id }}" class="flex items-center justify-between gap-2 py-3">
                <div class="flex items-center gap-2">
                    <flux:text>{{ $exercise->name }}</flux:text>

                    @if ($exercise->category)
                        <flux:badge size="sm">{{ $exercise->category->name }}</flux:badge>
                    @endif
                </div>

                <div class="flex items-center gap-1">
                    <flux:button
                        variant="ghost"
                        size="sm"
                        icon="pencil"
                        :href="route('exercises.edit', $exercise)"
                        wire:navigate
                    />
                    <flux:button
                        variant="ghost"
                        size="sm"
                        icon="trash"
                        wire:click="delete({{ $exercise->id }})"
                        wire:confirm="{{ __('Delete this exercise?') }}"
                    />
                </div>
            </flux:card>
        @empty
            <div class="p-8 text-center border rounded-lg border-zinc-200 dark:border-zinc-700">
                <p class="font-medium">{{ __('No exercises yet') }}</p>
                <flux:text class="mt-1">{{ __('Add your first exercise to build your catalog') }}</flux:text>
            </div>
        @endforelse
    </div>
</section>
