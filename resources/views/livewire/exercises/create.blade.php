<section class="w-full max-w-lg">
    <div class="flex items-center gap-3">
        <flux:button variant="ghost" icon="arrow-left" :href="route('exercises.index')" wire:navigate />

        <div>
            <flux:heading size="xl">{{ __('New exercise') }}</flux:heading>
            <flux:subheading>{{ __('Add an exercise to your catalog') }}</flux:subheading>
        </div>
    </div>

    <form wire:submit="save" class="mt-6 space-y-6">
        <flux:input wire:model="name" :label="__('Name')" :placeholder="__('e.g. Bench press')" required autofocus />

        <flux:select wire:model="category_id" :label="__('Category')" :placeholder="__('Select category')">
            @foreach ($this->categories as $category)
                <flux:select.option value="{{ $category->id }}">{{ $category->name }}</flux:select.option>
            @endforeach
        </flux:select>

        <flux:select wire:model="exercise_type_id" :label="__('Exercise type')" :placeholder="__('Select exercise type')">
            @foreach ($this->exerciseTypes as $exerciseType)
                <flux:select.option value="{{ $exerciseType->id }}">{{ $exerciseType->name }}</flux:select.option>
            @endforeach
        </flux:select>

        <div class="flex items-center gap-4">
            <flux:button type="submit" variant="primary">
                {{ __('Create exercise') }}
            </flux:button>
        </div>
    </form>
</section>
