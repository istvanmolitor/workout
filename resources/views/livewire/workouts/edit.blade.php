<section class="w-full max-w-2xl">
    <div class="flex items-center gap-3">
        <flux:button variant="ghost" icon="arrow-left" :href="route('workouts.index')" wire:navigate />

        <div>
            <flux:heading size="xl">{{ $workout->name }}</flux:heading>
            <flux:subheading>{{ __('Log how much you completed of each exercise') }}</flux:subheading>
        </div>
    </div>

    <form wire:submit="save" class="mt-6 space-y-6">
        @foreach ($workout->exercises as $exercise)
            <div wire:key="workout-exercise-{{ $exercise->id }}" class="space-y-2">
                <flux:heading>{{ $exercise->exercise->name }}</flux:heading>

                <div class="flex flex-wrap items-start gap-2">
                    @foreach ($exercise->sets as $set)
                        <div wire:key="workout-exercise-{{ $exercise->id }}-set-{{ $set->id }}" class="flex items-start gap-2">
                            <div>
                                <flux:input
                                    wire:model="exercises.{{ $exercise->id }}.sets.{{ $set->id }}.completed_reps"
                                    type="number"
                                    min="0"
                                    :label="__('Set :number (planned: :reps)', ['number' => $loop->iteration, 'reps' => $set->reps])"
                                    class="w-32"
                                />
                                <flux:error name="exercises.{{ $exercise->id }}.sets.{{ $set->id }}.completed_reps" />
                            </div>

                            <div>
                                <flux:input
                                    wire:model="exercises.{{ $exercise->id }}.sets.{{ $set->id }}.completed_weight"
                                    type="number"
                                    step="0.5"
                                    min="0"
                                    :label="$set->weight !== null ? __('Weight (planned: :weight kg)', ['weight' => rtrim(rtrim($set->weight, '0'), '.')]) : __('Weight (kg)')"
                                    class="w-32"
                                />
                                <flux:error name="exercises.{{ $exercise->id }}.sets.{{ $set->id }}.completed_weight" />
                            </div>
                        </div>
                    @endforeach

                </div>

                <flux:radio.group wire:model="exercises.{{ $exercise->id }}.difficulty" :label="__('Difficulty')">
                    @foreach ($this->difficultyLabels as $level => $difficultyLabel)
                        <flux:radio value="{{ $level }}" :label="$difficultyLabel" />
                    @endforeach
                </flux:radio.group>
                <flux:error name="exercises.{{ $exercise->id }}.difficulty" />

                <flux:separator />
            </div>
        @endforeach

        <div class="flex items-center gap-4">
            <flux:button type="submit" variant="primary">
                {{ __('Save workout') }}
            </flux:button>
        </div>
    </form>
</section>
