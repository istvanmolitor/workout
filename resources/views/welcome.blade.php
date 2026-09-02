<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
        <meta name="description" content="{{ __('Design workout plans exercise by exercise, perform them set by set, and track every rep, weight and rating along the way.') }}">
    </head>
    <body class="min-h-screen bg-white text-zinc-900 antialiased dark:bg-zinc-900 dark:text-zinc-100">
        {{-- Header --}}
        <header class="sticky top-0 z-30 border-b border-zinc-200 bg-white/80 backdrop-blur-sm dark:border-zinc-800 dark:bg-zinc-900/80">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-6 py-4 lg:px-8">
                <x-app-logo />

                <nav class="hidden items-center gap-8 text-sm font-medium text-zinc-600 md:flex dark:text-zinc-400">
                    <a href="#features" class="transition hover:text-zinc-900 dark:hover:text-white">{{ __('Features') }}</a>
                    <a href="#how-it-works" class="transition hover:text-zinc-900 dark:hover:text-white">{{ __('How it works') }}</a>
                </nav>

                <div class="flex items-center gap-2">
                    @auth
                        <flux:button variant="primary" :href="route('dashboard')" wire:navigate>
                            {{ __('Dashboard') }}
                        </flux:button>
                    @else
                        <flux:button variant="ghost" :href="route('login')" wire:navigate class="max-sm:hidden">
                            {{ __('Log in') }}
                        </flux:button>
                        <flux:button variant="primary" :href="route('register')" wire:navigate>
                            {{ __('Register') }}
                        </flux:button>
                    @endauth
                </div>
            </div>
        </header>

        <main>
            {{-- Hero --}}
            <section class="relative overflow-hidden">
                <div class="pointer-events-none absolute inset-0 -z-10 overflow-hidden">
                    <div class="absolute -top-40 left-1/2 h-[36rem] w-[36rem] -translate-x-1/3 rounded-full bg-orange-400/20 blur-3xl dark:bg-orange-500/10"></div>
                    <div class="absolute top-20 right-0 h-[28rem] w-[28rem] translate-x-1/3 rounded-full bg-blue-400/20 blur-3xl dark:bg-blue-500/10"></div>
                </div>

                <div class="mx-auto max-w-7xl px-6 pt-16 pb-20 lg:px-8 lg:pt-24 lg:pb-28">
                    <div class="grid items-center gap-16 lg:grid-cols-2">
                        <div>
                            <span class="inline-flex items-center gap-2 rounded-full border border-zinc-200 bg-zinc-50 px-3 py-1 text-xs font-medium text-zinc-600 dark:border-zinc-800 dark:bg-zinc-800/60 dark:text-zinc-300">
                                <flux:icon.sparkles class="size-3.5 text-orange-500" />
                                {{ __('Workout planning, reinvented') }}
                            </span>

                            <h1 class="mt-6 text-4xl font-bold tracking-tight text-balance sm:text-5xl lg:text-6xl">
                                {{ __('Train with a plan.') }}
                                <span class="text-orange-500">{{ __('Progress with proof.') }}</span>
                            </h1>

                            <p class="mt-6 max-w-xl text-lg text-zinc-600 text-pretty dark:text-zinc-400">
                                {{ __('Design workout plans exercise by exercise, perform them set by set, and track every rep, weight and rating along the way.') }}
                            </p>

                            <div class="mt-8 flex flex-wrap items-center gap-3">
                                @auth
                                    <flux:button variant="primary" icon:trailing="arrow-right" :href="route('dashboard')" wire:navigate>
                                        {{ __('Go to dashboard') }}
                                    </flux:button>
                                @else
                                    <flux:button variant="primary" icon:trailing="arrow-right" :href="route('register')" wire:navigate>
                                        {{ __('Start training free') }}
                                    </flux:button>
                                    <flux:button variant="ghost" :href="route('login')" wire:navigate>
                                        {{ __('Log in') }}
                                    </flux:button>
                                @endauth
                            </div>

                            <dl class="mt-10 grid grid-cols-1 gap-4 sm:grid-cols-3">
                                <div class="flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-400">
                                    <flux:icon.check-circle class="size-4 shrink-0 text-emerald-500" />
                                    {{ __('Free to get started') }}
                                </div>
                                <div class="flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-400">
                                    <flux:icon.shield-check class="size-4 shrink-0 text-emerald-500" />
                                    {{ __('Passkey secure sign-in') }}
                                </div>
                                <div class="flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-400">
                                    <flux:icon.bolt class="size-4 shrink-0 text-emerald-500" />
                                    {{ __('Built for consistency') }}
                                </div>
                            </dl>
                        </div>

                        <div class="relative mx-auto w-full max-w-md lg:mx-0">
                            <div class="absolute -top-6 -right-6 z-10 hidden w-48 rotate-3 rounded-xl border border-zinc-200 bg-white p-4 shadow-xl sm:block dark:border-zinc-700 dark:bg-zinc-800">
                                <div class="flex items-center gap-2 text-xs font-medium text-zinc-500 dark:text-zinc-400">
                                    <flux:icon.fire class="size-4 text-orange-500" />
                                    {{ __('Session difficulty') }}
                                </div>
                                <flux:heading class="mt-1">{{ __('Hard') }}</flux:heading>
                                <div class="mt-2 flex gap-1">
                                    @for ($i = 0; $i < 4; $i++)
                                        <flux:icon.fire class="size-4 text-orange-500" />
                                    @endfor
                                    <flux:icon.fire class="size-4 text-zinc-300 dark:text-zinc-600" />
                                </div>
                            </div>

                            <flux:card class="relative -rotate-1 space-y-4 shadow-2xl">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <flux:heading size="lg">{{ __('Push day') }}</flux:heading>
                                        <flux:subheading>{{ now()->translatedFormat('l, F j') }}</flux:subheading>
                                    </div>
                                    <flux:badge color="emerald" size="sm">{{ __('In progress') }}</flux:badge>
                                </div>

                                <flux:separator />

                                <ul class="space-y-3">
                                    <li class="flex items-center justify-between text-sm">
                                        <span class="flex items-center gap-2">
                                            <flux:icon.check-circle class="size-4 text-emerald-500" />
                                            {{ __('Bench press') }}
                                        </span>
                                        <flux:badge size="sm">4&times;60kg</flux:badge>
                                    </li>
                                    <li class="flex items-center justify-between text-sm">
                                        <span class="flex items-center gap-2">
                                            <flux:icon.check-circle class="size-4 text-emerald-500" />
                                            {{ __('Overhead press') }}
                                        </span>
                                        <flux:badge size="sm">3&times;30kg</flux:badge>
                                    </li>
                                    <li class="flex items-center justify-between text-sm">
                                        <span class="flex items-center gap-2">
                                            <span class="size-4 shrink-0 rounded-full border-2 border-dashed border-zinc-300 dark:border-zinc-600"></span>
                                            <span class="text-zinc-400 dark:text-zinc-500">{{ __('Triceps pushdown') }}</span>
                                        </span>
                                        <flux:badge size="sm" color="zinc">3&times;20kg</flux:badge>
                                    </li>
                                </ul>

                                <flux:button variant="primary" class="w-full!" icon="play">
                                    {{ __('Continue workout') }}
                                </flux:button>
                            </flux:card>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Features --}}
            <section id="features" class="border-t border-zinc-200 bg-zinc-50 py-20 dark:border-zinc-800 dark:bg-zinc-950/40">
                <div class="mx-auto max-w-7xl px-6 lg:px-8">
                    <div class="mx-auto max-w-2xl text-center">
                        <flux:heading size="xl" level="2">{{ __('Everything you need to train smarter') }}</flux:heading>
                        <flux:subheading class="mt-2">{{ __('From your first exercise to your next personal record.') }}</flux:subheading>
                    </div>

                    <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ([
                            ['icon' => 'clipboard-document-check', 'title' => 'Exercise catalog & categories', 'description' => 'Organize every movement into categories so your training library stays clear and easy to browse.'],
                            ['icon' => 'calendar-date-range', 'title' => 'Custom workout plans', 'description' => 'Build plans exercise by exercise with your own target sets, reps and weight.'],
                            ['icon' => 'play-circle', 'title' => 'Guided workout sessions', 'description' => 'Start a session from any plan and log your completed reps and weight, set by set, as you train.'],
                            ['icon' => 'fire', 'title' => 'Difficulty ratings', 'description' => "Rate how hard each exercise felt to spot when it's time to push harder or ease off."],
                            ['icon' => 'chart-bar', 'title' => 'Planned vs. completed tracking', 'description' => 'Compare what you planned against what you actually lifted to see your real progress over time.'],
                            ['icon' => 'shield-check', 'title' => 'Secure passkey login', 'description' => 'Sign in instantly with a passkey, no password to remember or leak.'],
                        ] as $feature)
                            <flux:card class="space-y-3">
                                <div class="flex size-10 items-center justify-center rounded-lg bg-orange-500/10 text-orange-500">
                                    <x-dynamic-component :component="'flux::icon.'.$feature['icon']" class="size-5" variant="micro" />
                                </div>
                                <flux:heading>{{ __($feature['title']) }}</flux:heading>
                                <flux:text>{{ __($feature['description']) }}</flux:text>
                            </flux:card>
                        @endforeach
                    </div>
                </div>
            </section>

            {{-- How it works --}}
            <section id="how-it-works" class="py-20">
                <div class="mx-auto max-w-7xl px-6 lg:px-8">
                    <div class="mx-auto max-w-2xl text-center">
                        <flux:heading size="xl" level="2">{{ __('How it works') }}</flux:heading>
                        <flux:subheading class="mt-2">{{ __('Three simple steps from plan to progress.') }}</flux:subheading>
                    </div>

                    <div class="relative mt-14 grid gap-10 sm:grid-cols-3">
                        <div class="absolute top-6 right-0 left-0 hidden h-px bg-zinc-200 sm:block dark:bg-zinc-800"></div>

                        @foreach ([
                            ['icon' => 'clipboard-document-check', 'title' => 'Build your plan', 'description' => 'Add exercises from your catalog and define your target sets, reps, weight and order.'],
                            ['icon' => 'play-circle', 'title' => 'Perform your workout', 'description' => 'Start a session from your plan and log your completed reps and weight as you go.'],
                            ['icon' => 'chart-bar', 'title' => 'Track your progress', 'description' => 'Rate the difficulty of every exercise and compare planned vs completed numbers over time.'],
                        ] as $index => $step)
                            <div class="relative text-center">
                                <div class="relative mx-auto flex size-12 items-center justify-center rounded-full border border-zinc-200 bg-white text-orange-500 dark:border-zinc-800 dark:bg-zinc-900">
                                    <x-dynamic-component :component="'flux::icon.'.$step['icon']" class="size-5" variant="micro" />
                                </div>
                                <flux:heading class="mt-4">{{ $index + 1 }}. {{ __($step['title']) }}</flux:heading>
                                <flux:text class="mx-auto mt-2 max-w-xs">{{ __($step['description']) }}</flux:text>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            {{-- CTA --}}
            <section class="border-t border-zinc-200 py-20 dark:border-zinc-800">
                <div class="mx-auto max-w-4xl px-6 text-center lg:px-8">
                    <flux:heading size="xl" level="2">{{ __('Ready to build your first plan?') }}</flux:heading>
                    <flux:subheading class="mt-2">{{ __('Create a free account and start planning your next workout in minutes.') }}</flux:subheading>

                    <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                        @auth
                            <flux:button variant="primary" icon:trailing="arrow-right" :href="route('dashboard')" wire:navigate>
                                {{ __('Go to dashboard') }}
                            </flux:button>
                        @else
                            <flux:button variant="primary" icon:trailing="arrow-right" :href="route('register')" wire:navigate>
                                {{ __('Create free account') }}
                            </flux:button>
                            <flux:button variant="ghost" :href="route('login')" wire:navigate>
                                {{ __('Log in') }}
                            </flux:button>
                        @endauth
                    </div>
                </div>
            </section>
        </main>

        <footer class="border-t border-zinc-200 py-10 dark:border-zinc-800">
            <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-4 px-6 sm:flex-row lg:px-8">
                <x-app-logo />
                <flux:text class="text-center sm:text-right">
                    &copy; {{ now()->year }} {{ config('app.name') }}. {{ __('All rights reserved.') }}
                </flux:text>
            </div>
        </footer>

        @fluxScripts
    </body>
</html>
