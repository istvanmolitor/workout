<section class="w-full">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Workout plans') }}</flux:heading>
            <flux:subheading>{{ __('Create and manage your workout plans') }}</flux:subheading>
        </div>

        <flux:button variant="primary" icon="plus" :href="route('workout-plans.create')" wire:navigate>
            {{ __('New workout plan') }}
        </flux:button>
    </div>

    <div class="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($this->workoutPlans as $workoutPlan)
            <flux:card wire:key="workout-plan-{{ $workoutPlan->id }}" class="space-y-3">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <flux:heading>{{ $workoutPlan->name }}</flux:heading>
                        @if ($workoutPlan->description)
                            <flux:text class="mt-1">{{ $workoutPlan->description }}</flux:text>
                        @endif
                    </div>

                    <flux:button
                        variant="ghost"
                        size="sm"
                        icon="pencil"
                        :href="route('workout-plans.edit', $workoutPlan)"
                        wire:navigate
                    />
                </div>

                <flux:separator />

                <ul class="space-y-1">
                    @foreach ($workoutPlan->exercises as $exercise)
                        <li class="flex items-center justify-between text-sm">
                            <span>{{ $exercise->exercise->name }}</span>
                            <flux:badge size="sm">{{ __(':sets x :reps', ['sets' => $exercise->sets, 'reps' => $exercise->reps]) }}</flux:badge>
                        </li>
                    @endforeach
                </ul>
            </flux:card>
        @empty
            <div class="col-span-full p-8 text-center border rounded-lg border-zinc-200 dark:border-zinc-700">
                <p class="font-medium">{{ __('No workout plans yet') }}</p>
                <flux:text class="mt-1">{{ __('Create your first workout plan to get started') }}</flux:text>
            </div>
        @endforelse
    </div>
</section>
