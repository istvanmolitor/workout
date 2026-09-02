<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white text-zinc-900 antialiased dark:bg-zinc-900 dark:text-zinc-100">
        <div class="relative flex min-h-svh flex-col items-center justify-center overflow-hidden p-6 md:p-10">
            <div class="pointer-events-none absolute inset-0 -z-10 overflow-hidden">
                <div class="absolute -top-40 left-1/2 h-[36rem] w-[36rem] -translate-x-1/3 rounded-full bg-orange-400/20 blur-3xl dark:bg-orange-500/10"></div>
                <div class="absolute top-20 right-0 h-[28rem] w-[28rem] translate-x-1/3 rounded-full bg-blue-400/20 blur-3xl dark:bg-blue-500/10"></div>
            </div>

            <div class="flex w-full max-w-sm flex-col gap-6">
                <a href="{{ route('home') }}" class="flex justify-center" wire:navigate>
                    <x-app-logo />
                </a>

                <flux:card class="flex flex-col gap-6">
                    {{ $slot }}
                </flux:card>
            </div>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
