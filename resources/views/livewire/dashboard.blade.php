<section class="w-full">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <flux:heading size="xl">{{ __('Dashboard') }}</flux:heading>
            <flux:subheading>{{ $this->monthLabel }}</flux:subheading>
        </div>

        <div class="flex items-center gap-1">
            <flux:button variant="ghost" size="sm" icon="chevron-left" wire:click="previousMonth" />
            <flux:button variant="ghost" size="sm" wire:click="goToToday">{{ __('Today') }}</flux:button>
            <flux:button variant="ghost" size="sm" icon="chevron-right" wire:click="nextMonth" />
        </div>
    </div>

    <div class="mt-6 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
        <div class="grid grid-cols-7 border-b border-neutral-200 bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-900">
            @foreach (['H', 'K', 'Sze', 'Cs', 'P', 'Szo', 'V'] as $weekday)
                <div class="p-2 text-center text-xs font-medium text-neutral-500 dark:text-neutral-400">
                    {{ $weekday }}
                </div>
            @endforeach
        </div>

        <div class="grid grid-cols-7">
            @foreach ($this->weeks as $week)
                @foreach ($week as $day)
                    @php
                        $dayWorkouts = $this->workoutsByDate->get($day->format('Y-m-d'), collect());
                        $isCurrentMonth = $day->month === $month;
                    @endphp

                    <div
                        wire:key="day-{{ $day->format('Y-m-d') }}"
                        @class([
                            'min-h-24 border-b border-r border-neutral-200 p-2 last:border-r-0 dark:border-neutral-700',
                            'bg-white dark:bg-neutral-800' => $isCurrentMonth,
                            'bg-neutral-50 dark:bg-neutral-900' => ! $isCurrentMonth,
                        ])
                    >
                        <span
                            @class([
                                'text-sm',
                                'text-neutral-400 dark:text-neutral-600' => ! $isCurrentMonth,
                                'font-semibold text-white flex size-6 items-center justify-center rounded-full bg-red-500' => $day->isToday(),
                            ])
                        >
                            {{ $day->day }}
                        </span>

                        <div class="mt-1 space-y-1">
                            @foreach ($dayWorkouts as $workout)
                                <flux:button
                                    wire:key="workout-{{ $workout->id }}"
                                    variant="ghost"
                                    size="sm"
                                    :href="route('workouts.edit', $workout)"
                                    wire:navigate
                                    class="!h-auto w-full !justify-start truncate !px-2 !py-1 text-xs"
                                >
                                    {{ $workout->name }}
                                </flux:button>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @endforeach
        </div>
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
