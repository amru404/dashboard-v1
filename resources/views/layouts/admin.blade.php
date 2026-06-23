<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — {{ config('app.name', 'Customer Area') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Rethink+Sans:wght@400;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-vd-neutral text-vd-on-surface antialiased">

<div class="min-h-screen flex flex-col">

    {{-- ─────────── Top nav ─────────── --}}
    <header class="sticky top-0 z-40 border-b border-vd-border bg-vd-surface/90 backdrop-blur-md">
        <div class="mx-auto flex max-w-screen-xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">

            {{-- Brand --}}
            <div class="flex items-center gap-6">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 shrink-0">
                    <span class="flex h-9 w-9 items-center justify-center rounded-md bg-vd-primary/20 border border-vd-primary/40">
                        <x-application-logo class="h-5 w-5 fill-current text-vd-primary" />
                    </span>
                    <span class="hidden sm:block">
                        <span class="block text-label-lg text-vd-on-surface leading-tight">Customer Area</span>
                        <span class="block text-[11px] text-vd-muted leading-tight">Admin Console</span>
                    </span>
                </a>

                {{-- Desktop nav --}}
                <nav class="hidden items-center gap-1 md:flex">
                    <a href="{{ route('admin.dashboard') }}"
                       class="{{ request()->routeIs('admin.dashboard') ? 'vd-nav-link-active' : 'vd-nav-link' }}">
                        Dashboard
                    </a>

                    @php
                        $navGroups = [
                            [
                                'label'  => 'Catalog',
                                'active' => request()->routeIs('admin.products.*') || request()->routeIs('admin.license-types.*'),
                                'links'  => [
                                    ['label' => 'Products',      'route' => 'admin.products.index',      'match' => 'admin.products.*'],
                                    ['label' => 'License Types', 'route' => 'admin.license-types.index', 'match' => 'admin.license-types.*'],
                                ],
                            ],
                            [
                                'label'  => 'Access',
                                'active' => request()->routeIs('admin.entitlements.*') || request()->routeIs('admin.licenses.*') || request()->routeIs('admin.download-items.*'),
                                'links'  => [
                                    ['label' => 'Entitlements', 'route' => 'admin.entitlements.index',   'match' => 'admin.entitlements.*'],
                                    ['label' => 'Licenses',     'route' => 'admin.licenses.index',       'match' => 'admin.licenses.*'],
                                    ['label' => 'Downloads',    'route' => 'admin.download-items.index', 'match' => 'admin.download-items.*'],
                                ],
                            ],
                            [
                                'label'  => 'Users',
                                'active' => request()->routeIs('admin.organizations.*') || request()->routeIs('admin.users.*'),
                                'links'  => [
                                    ['label' => 'Organizations', 'route' => 'admin.organizations.index', 'match' => 'admin.organizations.*'],
                                    ['label' => 'Users',         'route' => 'admin.users.index',         'match' => 'admin.users.*'],
                                ],
                            ],
                        ];
                    @endphp

                    @foreach ($navGroups as $group)
                        <x-dropdown align="left" width="w-52" contentClasses="bg-vd-surface border border-vd-border rounded-lg py-1 shadow-vd-lg" :open-on-hover="true">
                            <x-slot name="trigger">
                                <button type="button"
                                    class="{{ $group['active'] ? 'vd-nav-link-active' : 'vd-nav-link' }} cursor-pointer gap-1">
                                    {{ $group['label'] }}
                                    <svg class="h-3 w-3 opacity-60" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                @foreach ($group['links'] as $link)
                                    <a href="{{ route($link['route']) }}"
                                       class="flex items-center px-4 py-2.5 text-body-sm transition
                                              {{ request()->routeIs($link['match']) ? 'bg-vd-primary/15 text-vd-primary font-semibold' : 'text-vd-muted hover:bg-white/5 hover:text-vd-on-surface' }}">
                                        {{ $link['label'] }}
                                    </a>
                                @endforeach
                            </x-slot>
                        </x-dropdown>
                    @endforeach
                </nav>
            </div>

            {{-- Right side --}}
            <div class="flex items-center gap-3">
                <div class="hidden text-right sm:block">
                    <p class="text-label-sm text-vd-on-surface">{{ Auth::user()->name }}</p>
                    <p class="text-[11px] text-vd-muted">{{ Auth::user()->email }}</p>
                </div>
                <span class="vd-chip hidden sm:inline-flex">Admin</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="button" onclick="this.closest('form').submit()"
                        class="vd-btn-ghost text-vd-error hover:text-vd-error">
                        Sign out
                    </button>
                </form>
            </div>
        </div>

        {{-- Mobile nav --}}
        <div class="border-t border-vd-border px-4 py-2 md:hidden overflow-x-auto">
            <div class="flex gap-1 min-w-max">
                <a href="{{ route('admin.dashboard') }}"
                   class="{{ request()->routeIs('admin.dashboard') ? 'vd-nav-link-active' : 'vd-nav-link' }}">Dashboard</a>
                @foreach ($navGroups as $group)
                    @foreach ($group['links'] as $link)
                        <a href="{{ route($link['route']) }}"
                           class="{{ request()->routeIs($link['match']) ? 'vd-nav-link-active' : 'vd-nav-link' }}">
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                @endforeach
            </div>
        </div>
    </header>

    {{-- ─────────── Main ─────────── --}}
    <main class="flex-1 mx-auto w-full max-w-screen-xl px-4 py-8 sm:px-6 lg:px-8">
        <x-alert />
        @yield('content')
    </main>

    {{-- ─────────── Footer ─────────── --}}
    <footer class="border-t border-vd-border px-6 py-4 text-center text-[11px] text-vd-muted">
        Customer Area &mdash; Admin Console
    </footer>
</div>

</body>
</html>
