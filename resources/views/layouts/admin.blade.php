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
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-madani-deep text-white">
                                <x-application-logo class="h-6 w-6 fill-current" />
                            </span>
                            <span>
                                <span class="block text-base font-bold text-madani-deep">Customer Area</span>
                                <span class="block text-xs font-medium text-madani-muted">Admin console</span>
                            </span>
                        </a>

                        <div class="hidden items-center gap-2 md:flex">
                            <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">Dashboard</x-nav-link>

                            @php
                                $navGroups = [
                                    [
                                        'label' => 'Catalog',
                                        'active' => request()->routeIs('admin.products.*') || request()->routeIs('admin.license-types.*'),
                                        'links' => [
                                            ['label' => 'Products', 'route' => 'admin.products.index', 'active' => 'admin.products.*'],
                                            ['label' => 'Types', 'route' => 'admin.license-types.index', 'active' => 'admin.license-types.*'],
                                        ],
                                    ],
                                    [
                                        'label' => 'Access',
                                        'active' => request()->routeIs('admin.entitlements.*') || request()->routeIs('admin.licenses.*') || request()->routeIs('admin.download-items.*'),
                                        'links' => [
                                            ['label' => 'Entitlements', 'route' => 'admin.entitlements.index', 'active' => 'admin.entitlements.*'],
                                            ['label' => 'Licenses', 'route' => 'admin.licenses.index', 'active' => 'admin.licenses.*'],
                                            ['label' => 'Downloads', 'route' => 'admin.download-items.index', 'active' => 'admin.download-items.*'],
                                        ],
                                    ],
                                    [
                                        'label' => 'Users',
                                        'active' => request()->routeIs('admin.organizations.*') || request()->routeIs('admin.users.*'),
                                        'links' => [
                                            ['label' => 'Organizations', 'route' => 'admin.organizations.index', 'active' => 'admin.organizations.*'],
                                            ['label' => 'Users', 'route' => 'admin.users.index', 'active' => 'admin.users.*'],
                                        ],
                                    ],
                                ];
                            @endphp

                            @foreach ($navGroups as $group)
                                <x-dropdown align="left" width="w-56" contentClasses="bg-white py-2" :open-on-hover="true">
                                    <x-slot name="trigger">
                                        <button
                                            type="button"
                                            class="{{ $group['active'] ? 'inline-flex items-center rounded-lg bg-madani-pale px-4 py-2 text-sm font-semibold text-madani-deep transition duration-150 ease-in-out' : 'inline-flex items-center rounded-lg px-4 py-2 text-sm font-semibold text-madani-muted transition duration-150 ease-in-out hover:bg-madani-pale hover:text-madani-deep' }}"
                                        >
                                            {{ $group['label'] }}
                                            <span class="ml-2 h-0 w-0 border-x-4 border-t-4 border-x-transparent border-t-current" aria-hidden="true"></span>
                                        </button>
                                    </x-slot>

                                    <x-slot name="content">
                                        @foreach ($group['links'] as $link)
                                            <x-dropdown-link
                                                :href="route($link['route'])"
                                                class="{{ request()->routeIs($link['active']) ? 'bg-madani-pale font-semibold text-madani-deep' : '' }}"
                                            >
                                                {{ $link['label'] }}
                                            </x-dropdown-link>
                                        @endforeach
                                    </x-slot>
                                </x-dropdown>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="hidden text-right sm:block">
                            <p class="text-sm font-semibold text-madani-text">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-madani-muted">{{ Auth::user()->email }}</p>
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
                    <div class="mx-auto grid max-w-7xl gap-3">
                        <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">Dashboard</x-responsive-nav-link>

                        @foreach ($navGroups as $group)
                            <details class="rounded-lg border border-madani-border bg-white">
                                <summary class="{{ $group['active'] ? 'cursor-pointer rounded-lg bg-madani-pale px-4 py-2 text-sm font-semibold text-madani-deep' : 'cursor-pointer rounded-lg px-4 py-2 text-sm font-semibold text-madani-muted' }}">
                                    {{ $group['label'] }}
                                </summary>

                                <div class="grid gap-1 px-2 pb-2">
                                    @foreach ($group['links'] as $link)
                                        <x-responsive-nav-link :href="route($link['route'])" :active="request()->routeIs($link['active'])">
                                            {{ $link['label'] }}
                                        </x-responsive-nav-link>
                                    @endforeach
                                </div>
                            </details>
                        @endforeach
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
