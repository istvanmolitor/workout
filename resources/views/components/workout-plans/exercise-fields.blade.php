@props(['exercises'])

<div class="space-y-3">
    <flux:label>{{ __('Exercises') }}</flux:label>

    @foreach ($exercises as $index => $exercise)
        <div wire:key="exercise-row-{{ $index }}" class="flex items-start gap-2">
            <flux:input wire:model="exercises.{{ $index }}.name" :placeholder="__('Exercise name')" class="flex-1" />
            <flux:input wire:model="exercises.{{ $index }}.sets" type="number" min="1" :placeholder="__('Sets')" class="w-20" />
            <flux:input wire:model="exercises.{{ $index }}.reps" type="number" min="1" :placeholder="__('Reps')" class="w-20" />

            <flux:button
                type="button"
                variant="ghost"
                icon="trash"
                wire:click="removeExercise({{ $index }})"
                :disabled="count($exercises) <= 1"
            />
        </div>
        <flux:error name="exercises.{{ $index }}.name" />
        <flux:error name="exercises.{{ $index }}.sets" />
        <flux:error name="exercises.{{ $index }}.reps" />
    @endforeach

    <flux:button type="button" variant="outline" size="sm" icon="plus" wire:click="addExercise">
        {{ __('Add exercise') }}
    </flux:button>
</div>
