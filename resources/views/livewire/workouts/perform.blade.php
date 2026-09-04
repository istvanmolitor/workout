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
                    <div wire:key="workout-exercise-{{ $exercise->id }}-set-{{ $set->id }}" class="space-y-1">
                        <flux:text class="text-xs font-medium">{{ __('Set :number', ['number' => $loop->iteration]) }}</flux:text>

                        <div class="flex items-start gap-2">
                            @foreach ($set->values as $value)
                                @php($label = $value->field->unit ? "{$value->field->name} ({$value->field->unit})" : $value->field->name)
                                @php($label .= $value->value !== null ? ' '.__('(planned: :value)', ['value' => rtrim(rtrim($value->value, '0'), '.')]) : '')

                                <div>
                                    <flux:input
                                        wire:model="exercises.{{ $activeExerciseId }}.sets.{{ $set->id }}.values.{{ $value->field_id }}.completed_value"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        :label="$label"
                                        :autofocus="$loop->parent->first && $loop->first"
                                    />
                                    <flux:error name="exercises.{{ $activeExerciseId }}.sets.{{ $set->id }}.values.{{ $value->field_id }}.completed_value" />
                                </div>
                            @endforeach
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

            <div>
                <flux:textarea
                    wire:model="exercises.{{ $activeExerciseId }}.note"
                    :label="__('Note')"
                    :placeholder="__('Add a note about this exercise')"
                    rows="3"
                />
                <flux:error name="exercises.{{ $activeExerciseId }}.note" />
            </div>

            <flux:button type="submit" variant="primary" class="h-14 w-full text-lg">
                {{ __('Save and back') }}
            </flux:button>
        </form>
    @endif
</div>
