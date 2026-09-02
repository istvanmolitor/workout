<section class="w-full max-w-2xl">
    <div class="flex items-center gap-3">
        <flux:button variant="ghost" icon="arrow-left" :href="route('workout-plans.index')" wire:navigate />

        <div>
            <flux:heading size="xl">{{ __('New workout plan') }}</flux:heading>
            <flux:subheading>{{ __('Give it a name and list the exercises it includes') }}</flux:subheading>
        </div>
    </div>

    <form wire:submit="save" class="mt-6 space-y-6">
        <flux:input wire:model="name" :label="__('Name')" required autofocus />

        <flux:textarea wire:model="description" :label="__('Description')" rows="2" />

        <x-workout-plans.exercise-fields :exercises="$exercises" />

        <div class="flex items-center gap-4">
            <flux:button type="submit" variant="primary">
                {{ __('Create workout plan') }}
            </flux:button>
        </div>
    </form>
</section>
