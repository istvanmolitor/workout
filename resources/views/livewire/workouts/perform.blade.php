<div class="mx-auto flex min-h-screen w-full max-w-xl flex-col p-4">
    @if ($activeExerciseId === null)
        <div class="flex items-center gap-3">
            <flux:button variant="ghost" icon="x-mark" :href="route('workouts.index')" wire:navigate />
            <flux:heading size="xl">{{ $workout->name }}</flux:heading>
        </div>

        <div class="mt-6 grid gap-3">
            @foreach ($workout->exercises as $exercise)
                <flux:button
                    wire:key="workout-exercise-button-{{ $exercise->id }}"
                    :variant="$logged[$exercise->id] ? 'primary' : 'filled'"
                    :icon="$logged[$exercise->id] ? 'check' : null"
                    class="h-20 justify-start text-lg"
                    wire:click="selectExercise({{ $exercise->id }})"
                >
                    {{ $exercise->exercise->name }}
                </flux:button>
            @endforeach
        </div>
    @else
        @php($exercise = $workout->exercises->firstWhere('id', $activeExerciseId))

        <div class="flex items-center gap-3">
            <flux:button variant="ghost" icon="arrow-left" wire:click="back" />
            <flux:heading size="xl">{{ $exercise->exercise->name }}</flux:heading>
        </div>

        <form wire:submit="save" class="mt-8 space-y-6">
            <div class="space-y-4">
                @foreach ($exercise->sets as $set)
                    <div wire:key="workout-exercise-{{ $exercise->id }}-set-{{ $set->id }}" class="flex items-start gap-2">
                        <div>
                            <flux:input
                                wire:model="exercises.{{ $activeExerciseId }}.sets.{{ $set->id }}.completed_reps"
                                type="number"
                                min="0"
                                :label="__('Set :number (planned: :reps)', ['number' => $loop->iteration, 'reps' => $set->reps])"
                                :autofocus="$loop->first"
                            />
                            <flux:error name="exercises.{{ $activeExerciseId }}.sets.{{ $set->id }}.completed_reps" />
                        </div>

                        <div>
                            <flux:input
                                wire:model="exercises.{{ $activeExerciseId }}.sets.{{ $set->id }}.completed_weight"
                                type="number"
                                step="0.5"
                                min="0"
                                :label="$set->weight !== null ? __('Weight (planned: :weight kg)', ['weight' => rtrim(rtrim($set->weight, '0'), '.')]) : __('Weight (kg)')"
                            />
                            <flux:error name="exercises.{{ $activeExerciseId }}.sets.{{ $set->id }}.completed_weight" />
                        </div>
                    </div>
                @endforeach
            </div>

            <flux:radio.group wire:model="exercises.{{ $activeExerciseId }}.difficulty" :label="__('Difficulty')">
                @foreach ($this->difficultyLabels as $level => $difficultyLabel)
                    <flux:radio value="{{ $level }}" :label="$difficultyLabel" />
                @endforeach
            </flux:radio.group>
            <flux:error name="exercises.{{ $activeExerciseId }}.difficulty" />

            <flux:button type="submit" variant="primary" class="h-14 w-full text-lg">
                {{ __('Save and back') }}
            </flux:button>
        </form>
    @endif
</div>
