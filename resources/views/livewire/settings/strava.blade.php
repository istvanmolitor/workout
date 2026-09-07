<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading level="2" class="sr-only">{{ __('Strava settings') }}</flux:heading>

    <x-settings.layout :heading="__('Strava')" :subheading="__('Connect your Strava account to sync your training data')">
        <div class="flex items-center justify-between gap-4 rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
            <div class="flex items-center gap-4">
                <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-zinc-100 dark:bg-zinc-800">
                    <flux:icon.link class="size-5 text-zinc-500 dark:text-zinc-400" />
                </div>

                <div>
                    <p class="font-medium tracking-tight">Strava</p>
                    <p class="text-zinc-500 dark:text-zinc-400 text-sm">
                        @if ($this->connected)
                            {{ __('Your account is connected') }}
                        @else
                            {{ __('Your account is not connected') }}
                        @endif
                    </p>
                </div>
            </div>

            @if ($this->connected)
                <flux:button variant="danger" wire:click="disconnect">{{ __('Disconnect') }}</flux:button>
            @else
                <flux:button variant="primary" :href="route('strava.redirect')">{{ __('Connect Strava') }}</flux:button>
            @endif
        </div>
    </x-settings.layout>
</section>
