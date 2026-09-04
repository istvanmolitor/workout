<section class="w-full max-w-lg">
    <div class="flex items-center gap-3">
        <flux:button variant="ghost" icon="arrow-left" :href="route('exercise-types.index')" wire:navigate />

        <div>
            <flux:heading size="xl">{{ __('New exercise type') }}</flux:heading>
            <flux:subheading>{{ __('Define a type and the fields its exercises track') }}</flux:subheading>
        </div>
    </div>

    <form wire:submit="save" class="mt-6 space-y-6">
        <flux:input wire:model="name" :label="__('Name')" :placeholder="__('e.g. Running')" required autofocus />

        <flux:checkbox wire:model="single_set" :label="__('Only one set')" :description="__('Workout plans and workouts using this type will have exactly one set per exercise')" />

        <x-exercise-types.field-rows :fields="$fields" :available-fields="$this->availableFields" />

        <div class="flex items-center gap-4">
            <flux:button type="submit" variant="primary">
                {{ __('Create exercise type') }}
            </flux:button>
        </div>
    </form>
</section>
