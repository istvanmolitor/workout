<section class="w-full">
    <div>
        <flux:heading size="xl">{{ __('Dashboard') }}</flux:heading>
    </div>

    <div class="mt-6">
        <flux:heading size="lg">{{ __('Last workout') }}</flux:heading>

        @if ($this->lastWorkout)
            <flux:card class="mt-4 space-y-3">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <flux:heading>{{ $this->lastWorkout->name }}</flux:heading>
                        <flux:text class="mt-1">{{ $this->lastWorkout->performed_at->translatedFormat('Y. m. d.') }}</flux:text>
                    </div>

                    <flux:button variant="primary" size="sm" icon="plus" :href="route('workout-plans.index')" wire:navigate>
                        {{ __('New workout') }}
                    </flux:button>
                </div>

                <flux:separator />

                <ul class="space-y-1">
                    @foreach ($this->lastWorkout->exercises as $exercise)
                        <li class="flex items-center justify-between text-sm">
                            <span>{{ $exercise->exercise->name }}</span>
                            @if ($exercise->sets->contains(fn ($set) => $set->values->contains(fn ($value) => $value->completed_value !== null)))
                                <flux:badge size="sm">
                                    {{ $exercise->sets->map(fn ($set) => $set->values->map(fn ($value) => rtrim(rtrim($value->completed_value ?? '?', '0'), '.').($value->field->unit ? ' '.$value->field->unit : ''))->join('×'))->join(', ') }}
                                    @if ($exercise->difficulty !== null)
                                        &middot; {{ $exercise->difficulty }}/5
                                    @endif
                                </flux:badge>
                            @else
                                <flux:badge size="sm">{{ __('Not logged yet') }}</flux:badge>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </flux:card>
        @else
            <div class="mt-4 p-8 text-center border rounded-lg border-zinc-200 dark:border-zinc-700">
                <p class="font-medium">{{ __('No workouts yet') }}</p>
                <flux:text class="mt-1">{{ __('Start a workout from one of your workout plans to get going') }}</flux:text>

                <flux:button variant="primary" size="sm" icon="plus" class="mt-4" :href="route('workout-plans.index')" wire:navigate>
                    {{ __('New workout') }}
                </flux:button>
            </div>
        @endif
    </div>

    <div class="mt-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <flux:heading size="lg">{{ __('Body weight') }}</flux:heading>
                <flux:subheading>{{ __('Your weight trend over your most recent entries') }}</flux:subheading>
            </div>

            <flux:button variant="ghost" size="sm" icon="plus" :href="route('body-weights.create')" wire:navigate>
                {{ __('Log weight') }}
            </flux:button>
        </div>

        <div class="mt-4 overflow-hidden rounded-xl border border-neutral-200 bg-white p-4 dark:border-neutral-700 dark:bg-neutral-800">
            @if ($this->bodyWeights->count() >= 2)
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-semibold text-neutral-900 dark:text-white">
                        {{ number_format((float) $this->bodyWeights->last()->weight, 2) }} kg
                    </span>
                    <flux:text size="sm">{{ __('latest') }}</flux:text>
                </div>

                <svg viewBox="0 0 100 40" preserveAspectRatio="none" class="mt-4 h-32 w-full text-red-500">
                    <polyline
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.5"
                        vector-effect="non-scaling-stroke"
                        points="{{ collect($this->bodyWeightChartPoints)->map(fn (array $point) => "{$point['x']},{$point['y']}")->implode(' ') }}"
                    />
                </svg>

                <div class="mt-2 flex justify-between text-xs text-neutral-500 dark:text-neutral-400">
                    <span>{{ $this->bodyWeights->first()->measured_at->translatedFormat('Y. F j.') }}</span>
                    <span>{{ $this->bodyWeights->last()->measured_at->translatedFormat('Y. F j.') }}</span>
                </div>
            @elseif ($this->bodyWeights->count() === 1)
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-semibold text-neutral-900 dark:text-white">
                        {{ number_format((float) $this->bodyWeights->first()->weight, 2) }} kg
                    </span>
                    <flux:text size="sm">{{ $this->bodyWeights->first()->measured_at->translatedFormat('Y. F j.') }}</flux:text>
                </div>

                <flux:text class="mt-2">{{ __('Log another entry to see your trend on a chart') }}</flux:text>
            @else
                <p class="font-medium">{{ __('No body weight entries yet') }}</p>
                <flux:text class="mt-1">{{ __('Log your first weight to start tracking your progress') }}</flux:text>
            @endif
        </div>
    </div>
</section>
