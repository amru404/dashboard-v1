<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Customer Area') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-madani-text antialiased">
        <div class="min-h-screen bg-madani-offwhite">
            <div class="grid min-h-screen lg:grid-cols-[1.1fr_0.9fr]">
                <section class="hidden bg-madani-depth px-10 py-10 text-white lg:flex lg:flex-col lg:justify-between">
                    <a href="/" class="inline-flex items-center gap-3">
                        <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-white/10 ring-1 ring-white/15">
                            <x-application-logo class="h-7 w-7 fill-current text-white" />
                        </span>
                        <span class="text-lg font-bold tracking-normal">Customer Area</span>
                    </a>

                    <div class="max-w-xl">
                        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-white/60">License portal</p>
                        <h1 class="mt-5 text-5xl font-extrabold leading-tight tracking-normal">Customer Area</h1>
                        <p class="mt-6 text-base leading-8 text-white/75">
                            A private software distribution backend for organizations, users, license access, and installer verification.
                        </p>
                    </div>

                    <div class="rounded-2xl border border-white/10 bg-white/[0.06] p-6 text-sm leading-7 text-white/70 shadow-madani-lg">
                        Built for internal license management with clean access boundaries for admins, customers, and desktop installers.
                    </div>
                </section>

                <main class="flex min-h-screen items-center justify-center px-6 py-10">
                    <div class="w-full max-w-md">
                        <div class="mb-8 flex items-center gap-3 lg:hidden">
                            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-madani-deep text-white">
                                <x-application-logo class="h-7 w-7 fill-current" />
                            </span>
                            <span class="text-lg font-bold text-madani-deep">Customer Area</span>
                        </div>

                        <div class="madani-card px-6 py-7 sm:px-8">
                            {{ $slot }}
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>
