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

        <flux:badge class="mt-2 w-fit" size="lg">
            {{ __('Planned: :sets x :reps', ['sets' => $exercise->sets, 'reps' => $exercise->reps]) }}
        </flux:badge>

        <form wire:submit="save" class="mt-8 space-y-6">
            <flux:input
                wire:model="exercises.{{ $activeExerciseId }}.completed_sets"
                type="number"
                min="0"
                :label="__('Completed sets')"
                autofocus
            />
            <flux:input
                wire:model="exercises.{{ $activeExerciseId }}.completed_reps"
                type="number"
                min="0"
                :label="__('Completed reps')"
            />
            <flux:input
                wire:model="exercises.{{ $activeExerciseId }}.difficulty"
                type="number"
                min="1"
                max="10"
                :label="__('Difficulty (1-10)')"
            />

            <flux:error name="exercises.{{ $activeExerciseId }}.completed_sets" />
            <flux:error name="exercises.{{ $activeExerciseId }}.completed_reps" />
            <flux:error name="exercises.{{ $activeExerciseId }}.difficulty" />

            <flux:button type="submit" variant="primary" class="h-14 w-full text-lg">
                {{ __('Save and back') }}
            </flux:button>
        </form>
    @endif
</div>
