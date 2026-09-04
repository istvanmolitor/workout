@props(['exercises', 'availableExercises', 'fieldsForExercise', 'isSingleSet'])

<div class="space-y-4">
    <flux:label>{{ __('Exercises') }}</flux:label>

    @foreach ($exercises as $index => $exercise)
        <div wire:key="exercise-row-{{ $index }}" class="space-y-2 rounded-lg border border-zinc-200 p-3 dark:border-zinc-700">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-start">
                <flux:select wire:model.live="exercises.{{ $index }}.exercise_id" :placeholder="__('Select exercise')" class="min-w-0 sm:flex-1">
                    @foreach ($availableExercises as $availableExercise)
                        <flux:select.option value="{{ $availableExercise->id }}">{{ $availableExercise->name }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:button
                    type="button"
                    variant="ghost"
                    icon="trash"
                    wire:click="removeExercise({{ $index }})"
                    :disabled="count($exercises) <= 1"
                />
            </div>
            <flux:error name="exercises.{{ $index }}.exercise_id" />

            @php($fields = $exercise['exercise_id'] !== '' ? $fieldsForExercise($exercise['exercise_id']) : collect())

            <div class="flex flex-wrap items-start gap-3">
                @foreach ($exercise['sets'] as $setIndex => $set)
                    <div wire:key="exercise-row-{{ $index }}-set-{{ $setIndex }}" class="space-y-1">
                        <flux:text class="text-xs font-medium">{{ __('Set :number', ['number' => $setIndex + 1]) }}</flux:text>

                        <div class="flex items-start gap-1">
                            @foreach ($fields as $fieldId => $field)
                                <div>
                                    <flux:input
                                        wire:model="exercises.{{ $index }}.sets.{{ $setIndex }}.values.{{ $fieldId }}"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        :label="$field->unit ? $field->name.' ('.$field->unit.')' : $field->name"
                                        class="w-24"
                                    />
                                    <flux:error name="exercises.{{ $index }}.sets.{{ $setIndex }}.values.{{ $fieldId }}" />
                                </div>
                            @endforeach

                            <flux:button
                                type="button"
                                variant="ghost"
                                size="sm"
                                icon="x-mark"
                                wire:click="removeSet({{ $index }}, {{ $setIndex }})"
                                :disabled="count($exercise['sets']) <= 1"
                            />
                        </div>
                    </div>
                @endforeach

                @if ($exercise['exercise_id'] !== '' && $isSingleSet($exercise['exercise_id']))
                    <flux:text class="text-xs">{{ __('This exercise type only allows a single set.') }}</flux:text>
                @else
                    <flux:button
                        type="button"
                        variant="outline"
                        size="sm"
                        icon="plus"
                        wire:click="addSet({{ $index }})"
                        :disabled="$exercise['exercise_id'] === ''"
                    >
                        {{ __('Add set') }}
                    </flux:button>
                @endif
            </div>
            <flux:error name="exercises.{{ $index }}.sets" />
        </div>
    @endforeach

    <flux:button type="button" variant="outline" size="sm" icon="plus" wire:click="addExercise">
        {{ __('Add exercise') }}
    </flux:button>
</div>
