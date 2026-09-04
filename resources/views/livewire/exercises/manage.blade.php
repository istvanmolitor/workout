<section class="w-full max-w-5xl">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Exercises') }}</flux:heading>
            <flux:subheading>{{ __('Manage the exercise catalog used in your workout plans') }}</flux:subheading>
        </div>

        <flux:button variant="primary" icon="plus" :href="route('exercises.create')" wire:navigate>
            {{ __('New exercise') }}
        </flux:button>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($this->exercises as $exercise)
            <flux:card wire:key="exercise-{{ $exercise->id }}" class="flex flex-col gap-3">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-zinc-100 dark:bg-zinc-800">
                        <flux:icon :icon="$exercise->category?->icon() ?? 'dumbbell'" class="size-5 text-zinc-500 dark:text-zinc-400" />
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
                </div>

                <div>
                    <flux:text class="font-medium">{{ $exercise->name }}</flux:text>

                    <div class="mt-2 flex flex-wrap gap-1">
                        @if ($exercise->category)
                            <flux:badge size="sm">{{ $exercise->category->name }}</flux:badge>
                        @endif
                        <flux:badge size="sm" color="zinc">{{ $exercise->exerciseType->name }}</flux:badge>
                    </div>
                </div>
            </flux:card>
        @empty
            <div class="p-8 text-center border rounded-lg border-zinc-200 dark:border-zinc-700 sm:col-span-2 lg:col-span-3">
                <p class="font-medium">{{ __('No exercises yet') }}</p>
                <flux:text class="mt-1">{{ __('Add your first exercise to build your catalog') }}</flux:text>
            </div>
        @endforelse
    </div>
</section>
