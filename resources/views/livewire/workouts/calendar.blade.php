<section class="w-full">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <flux:heading size="xl">{{ __('Workout calendar') }}</flux:heading>
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
</section>
