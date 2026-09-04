@props(['fields', 'availableFields'])

<div class="space-y-2">
    <flux:label>{{ __('Fields') }}</flux:label>

    @foreach ($fields as $index => $field)
        <div wire:key="field-row-{{ $index }}">
            <div class="flex items-start gap-2">
                <flux:select wire:model="fields.{{ $index }}.field_id" :placeholder="__('Select field')" class="min-w-0 flex-1">
                    @foreach ($availableFields as $availableField)
                        <flux:select.option value="{{ $availableField->id }}">
                            {{ $availableField->unit ? "{$availableField->name} ({$availableField->unit})" : $availableField->name }}
                        </flux:select.option>
                    @endforeach
                </flux:select>

                <flux:button
                    type="button"
                    variant="ghost"
                    icon="trash"
                    wire:click="removeField({{ $index }})"
                    :disabled="count($fields) <= 1"
                />
            </div>
            <flux:error name="fields.{{ $index }}.field_id" />
        </div>
    @endforeach

    <flux:button type="button" variant="outline" size="sm" icon="plus" wire:click="addField">
        {{ __('Add field') }}
    </flux:button>
</div>
