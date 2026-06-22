<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name', 'Customer Area'))</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-madani-offwhite">
            <nav class="border-b border-madani-border bg-white">
                <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                    <div class="flex items-center gap-8">
                        <a href="{{ route('user.dashboard') }}" class="flex items-center gap-3">
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-madani-deep text-white">
                                <x-application-logo class="h-6 w-6 fill-current" />
                            </span>
                            <span>
                                <span class="block text-base font-bold text-madani-deep">Customer Area</span>
                                <span class="block text-xs font-medium text-madani-muted">Customer portal</span>
                            </span>
                        </a>

                        <div class="hidden items-center gap-2 md:flex">
                            <x-nav-link :href="route('user.dashboard')" :active="request()->routeIs('user.dashboard')">Dashboard</x-nav-link>
                            <x-nav-link :href="route('user.products.index')" :active="request()->routeIs('user.products.*')">Products</x-nav-link>
                            <x-nav-link :href="route('user.licenses.index')" :active="request()->routeIs('user.licenses.*')">Licenses</x-nav-link>
                            <x-nav-link :href="route('user.downloads.index')" :active="request()->routeIs('user.downloads.*')">Downloads</x-nav-link>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="hidden text-right sm:block">
                            <p class="text-sm font-semibold text-madani-text">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-madani-muted">{{ Auth::user()->organization?->name ?? 'No organization' }}</p>
                        </div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="rounded-lg border border-madani-border px-4 py-2 text-sm font-semibold text-madani-deep transition hover:border-madani-green hover:text-madani-green">
                                Log out
                            </button>
                        </form>
                    </div>
                </div>

                <div class="border-t border-madani-border px-4 py-3 md:hidden">
                    <div class="mx-auto flex max-w-7xl gap-2 overflow-x-auto">
                        <x-responsive-nav-link :href="route('user.dashboard')" :active="request()->routeIs('user.dashboard')">Dashboard</x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('user.products.index')" :active="request()->routeIs('user.products.*')">Products</x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('user.licenses.index')" :active="request()->routeIs('user.licenses.*')">Licenses</x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('user.downloads.index')" :active="request()->routeIs('user.downloads.*')">Downloads</x-responsive-nav-link>
                    </div>
                </div>
            </nav>

            <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                <x-alert />
                @yield('content')
            </main>
        </div>
    </body>
</html>
