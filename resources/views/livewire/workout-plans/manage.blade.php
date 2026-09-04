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

                    <div class="flex items-center gap-1">
                        <flux:button
                            variant="ghost"
                            size="sm"
                            icon="play"
                            wire:click="startWorkout({{ $workoutPlan->id }})"
                            :disabled="$workoutPlan->exercises->isEmpty()"
                        >
                            {{ __('Start workout') }}
                        </flux:button>

                        <flux:button
                            variant="ghost"
                            size="sm"
                            icon="pencil"
                            :href="route('workout-plans.edit', $workoutPlan)"
                            wire:navigate
                        />

                        <flux:modal.trigger name="confirm-workout-plan-deletion-{{ $workoutPlan->id }}">
                            <flux:button variant="ghost" size="sm" icon="trash" />
                        </flux:modal.trigger>
                    </div>
                </div>

                <flux:separator />

                <ul class="space-y-1">
                    @foreach ($workoutPlan->exercises as $exercise)
                        <li class="flex items-center justify-between text-sm">
                            <span>{{ $exercise->exercise->name }}</span>
                            <flux:badge size="sm">
                                {{ $exercise->sets->map(fn ($set) => $set->values->map(fn ($value) => rtrim(rtrim($value->value, '0'), '.').($value->field->unit ? ' '.$value->field->unit : ''))->join('×'))->join(', ') }}
                            </flux:badge>
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

    @foreach ($this->workoutPlans as $workoutPlan)
        <flux:modal name="confirm-workout-plan-deletion-{{ $workoutPlan->id }}" focusable class="max-w-lg">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">{{ __('Delete workout plan?') }}</flux:heading>

                    <flux:subheading>
                        {{ __('Are you sure you want to delete this workout plan? This action cannot be undone.') }}
                    </flux:subheading>
                </div>

                <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                    <flux:modal.close>
                        <flux:button variant="filled">{{ __('Cancel') }}</flux:button>
                    </flux:modal.close>

                    <flux:modal.close>
                        <flux:button variant="danger" wire:click="deleteWorkoutPlan({{ $workoutPlan->id }})">
                            {{ __('Delete') }}
                        </flux:button>
                    </flux:modal.close>
                </div>
            </div>
        </flux:modal>
    @endforeach
</section>
