<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading level="2" class="sr-only">{{ __('Profile settings') }}</flux:heading>

    <x-settings.layout :heading="__('Profile')" :subheading="__('Update your name and email address')">
        <div class="flex items-center gap-4">
            <flux:avatar
                size="xl"
                :src="$avatar ? $avatar->temporaryUrl() : Auth::user()->avatar_url"
                :name="Auth::user()->name"
                :initials="Auth::user()->initials()"
            />

            <div class="space-y-2">
                <flux:input type="file" wire:model="avatar" :label="__('Profile picture')" accept="image/*" />

                <div class="flex items-center gap-2">
                    <flux:button size="sm" variant="primary" wire:click="updateAvatar" wire:target="avatar">
                        {{ __('Upload') }}
                    </flux:button>

                    @if (Auth::user()->avatar)
                        <flux:button size="sm" variant="ghost" wire:click="removeAvatar">
                            {{ __('Remove avatar') }}
                        </flux:button>
                    @endif
                </div>
            </div>
        </div>

        <flux:separator class="my-6" />

        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <flux:input wire:model="name" :label="__('Name')" type="text" required autofocus autocomplete="name" />

            <div>
                <flux:input wire:model="email" :label="__('Email')" type="email" required autocomplete="email" />

                @if ($this->hasUnverifiedEmail)
                    <div>
                        <flux:text class="mt-4">
                            {{ __('Your email address is unverified.') }}

                            <flux:link class="text-sm cursor-pointer" wire:click.prevent="resendVerificationNotification">
                                {{ __('Click here to re-send the verification email.') }}
                            </flux:link>
                        </flux:text>

                    </div>
                @endif
            </div>

            <div class="flex items-center gap-4">
                <flux:button variant="primary" type="submit">{{ __('Save') }}</flux:button>
            </div>
        </form>

        @if ($this->showDeleteUser)
            <livewire:settings.delete-user-form />
        @endif
    </x-settings.layout>
</section>
