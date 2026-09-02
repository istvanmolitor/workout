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
                <div class="flex items-center justify-between gap-2">
                    <flux:heading>{{ $exercise->exercise->name }}</flux:heading>
                    <flux:badge size="sm">{{ __('Planned: :sets x :reps', ['sets' => $exercise->sets, 'reps' => $exercise->reps]) }}</flux:badge>
                </div>

                <div class="flex flex-wrap items-start gap-2">
                    <flux:input wire:model="exercises.{{ $exercise->id }}.completed_sets" type="number" min="0" :label="__('Completed sets')" class="w-32" />
                    <flux:input wire:model="exercises.{{ $exercise->id }}.completed_reps" type="number" min="0" :label="__('Completed reps')" class="w-32" />
                    <flux:input wire:model="exercises.{{ $exercise->id }}.difficulty" type="number" min="1" max="10" :label="__('Difficulty (1-10)')" class="w-32" />
                </div>

                <flux:error name="exercises.{{ $exercise->id }}.completed_sets" />
                <flux:error name="exercises.{{ $exercise->id }}.completed_reps" />
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
