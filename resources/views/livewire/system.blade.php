<section class="w-full max-w-5xl">
    <div>
        <flux:heading size="xl">{{ __('System') }}</flux:heading>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ([
            ['route' => 'exercises.index', 'icon' => 'list-bullet', 'label' => __('Exercises'), 'description' => __('Manage the exercise catalog used in your workout plans')],
            ['route' => 'exercise-categories.index', 'icon' => 'tag', 'label' => __('Exercise categories'), 'description' => __('Manage the categories used to organize your exercises')],
            ['route' => 'exercise-types.index', 'icon' => 'squares-2x2', 'label' => __('Exercise types'), 'description' => __('Manage the types that determine which fields exercises track')],
            ['route' => 'fields.index', 'icon' => 'variable', 'label' => __('Fields'), 'description' => __('Manage the trackable fields used by exercise types')],
            ['route' => 'foods.index', 'icon' => 'cake', 'label' => __('Foods'), 'description' => __('Manage the food catalog used when logging meals')],
        ] as $tile)
            <a href="{{ route($tile['route']) }}" wire:navigate wire:key="tile-{{ $tile['route'] }}">
                <flux:card class="flex h-full flex-col gap-3 transition hover:border-zinc-300 dark:hover:border-zinc-600">
                    <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-zinc-100 dark:bg-zinc-800">
                        <flux:icon :icon="$tile['icon']" class="size-5 text-zinc-500 dark:text-zinc-400" />
                    </div>

                    <div>
                        <flux:heading>{{ $tile['label'] }}</flux:heading>
                        <flux:text class="mt-1">{{ $tile['description'] }}</flux:text>
                    </div>
                </flux:card>
            </a>
        @endforeach
    </div>
</section>
