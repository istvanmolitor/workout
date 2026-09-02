<section class="w-full max-w-2xl">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Exercise categories') }}</flux:heading>
            <flux:subheading>{{ __('Manage the categories used to organize your exercises') }}</flux:subheading>
        </div>

        <flux:button variant="primary" icon="plus" :href="route('exercise-categories.create')" wire:navigate>
            {{ __('New category') }}
        </flux:button>
    </div>

    <div class="mt-6 space-y-2">
        @forelse ($this->exerciseCategories as $exerciseCategory)
            <flux:card wire:key="exercise-category-{{ $exerciseCategory->id }}" class="flex items-center justify-between gap-2 py-3">
                <flux:text>{{ $exerciseCategory->name }}</flux:text>

                <div class="flex items-center gap-1">
                    <flux:button
                        variant="ghost"
                        size="sm"
                        icon="pencil"
                        :href="route('exercise-categories.edit', $exerciseCategory)"
                        wire:navigate
                    />
                    <flux:button
                        variant="ghost"
                        size="sm"
                        icon="trash"
                        wire:click="delete({{ $exerciseCategory->id }})"
                        wire:confirm="{{ __('Delete this exercise category?') }}"
                    />
                </div>
            </flux:card>
        @empty
            <div class="p-8 text-center border rounded-lg border-zinc-200 dark:border-zinc-700">
                <p class="font-medium">{{ __('No exercise categories yet') }}</p>
                <flux:text class="mt-1">{{ __('Add your first category to organize your exercises') }}</flux:text>
            </div>
        @endforelse
    </div>
</section>
