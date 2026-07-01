<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — {{ config('app.name', 'Vericotech') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Rethink+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('img/favicon.svg') }}">


    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-vd-neutral text-vd-on-surface antialiased" style="background-image: linear-gradient(75deg, #030b15, #013169);">

<div class="min-h-screen flex flex-col">

    {{-- ─────────── Top nav ─────────── --}}
   <header class="sticky top-0 z-40 border-b border-vd-border bg-transparent backdrop-blur-md min-h-[80px]">
       
    {{-- logo --}}
        <div class="mx-auto flex w-full max-w-screen-xl items-center px-4 py-3 sm:px-6 lg:px-8">
             <div class="flex items-center gap-6">
                <a href="{{ route('user.dashboard') }}" class="flex items-center gap-3 shrink-0">
                    <span>
                        <x-application-logo class="h-2 w-2 fill-current text-vd-primary" />
                    </span>
                </a>
            </div>
            
            <div class="flex-1 flex justify-center">
                <nav class="hidden items-center gap-1 md:flex">
                    <a href="{{ route('admin.dashboard') }}"
                       class="{{ request()->routeIs('admin.dashboard') ? 'vd-nav-link-active' : 'vd-nav-link' }}">
                        Overview
                    </a>

                    @php
                        $navGroups = [
                            [
                                'label'  => 'Products',
                                'active' => request()->routeIs('admin.products.*') || request()->routeIs('admin.download-items.*'),
                                'links'  => [
                                    ['label' => 'Products',  'route' => 'admin.products.index',      'match' => 'admin.products.*'],
                                    ['label' => 'Downloads', 'route' => 'admin.download-items.index', 'match' => 'admin.download-items.*'],
                                ],
                            ],
                            [
                                'label'  => 'Licensing',
                                'active' => request()->routeIs('admin.license-types.*') || request()->routeIs('admin.licenses.*') || request()->routeIs('admin.entitlements.*'),
                                'links'  => [
                                    ['label' => 'License Types', 'route' => 'admin.license-types.index', 'match' => 'admin.license-types.*'],
                                    ['label' => 'Licenses',      'route' => 'admin.licenses.index',       'match' => 'admin.licenses.*'],
                                    ['label' => 'Entitlements',  'route' => 'admin.entitlements.index',   'match' => 'admin.entitlements.*'],
                                ],
                            ],
                            [
                                'label'  => 'Administration',
                                'active' => request()->routeIs('admin.organizations.*') || request()->routeIs('admin.users.*'),
                                'links'  => [
                                    ['label' => 'Organizations', 'route' => 'admin.organizations.index', 'match' => 'admin.organizations.*'],
                                    ['label' => 'Users',         'route' => 'admin.users.index',         'match' => 'admin.users.*'],
                                ],
                            ],
                            [
                                'label'  => 'Documents',
                                'active' => request()->routeIs('admin.invoices.*') || request()->routeIs('admin.quotations.*'),
                                'links'  => [
                                    ['label' => 'Invoices',   'route' => 'admin.invoices.index',    'match' => 'admin.invoices.*'],
                                    ['label' => 'Quotations', 'route' => 'admin.quotations.index',  'match' => 'admin.quotations.*'],
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
                <details class="relative">
                    <summary class="flex items-center gap-3 list-none cursor-pointer">
                        <div class="hidden text-right sm:block">
                            <p class="text-label-sm text-vd-on-surface">{{ Auth::user()->name }}</p>
                            <p class="text-[11px] text-vd-muted">{{ Auth::user()->organization?->name ?? 'No organization' }}</p>
                        </div>
                        <svg class="h-4 w-4 text-vd-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </summary>
                    <div class="absolute right-0 mt-2 w-44 rounded-md bg-vd-surface shadow-lg z-50">
                        <a href="{{ route('admin.settings.index') }}" class="block px-4 py-2 text-sm text-vd-on-surface hover:bg-vd-border">Help</a>
                        <a href="{{ route('admin.settings.index') }}" class="block px-4 py-2 text-sm text-vd-on-surface hover:bg-vd-border">Settings</a>
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-vd-on-surface hover:bg-vd-border">Profile</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-vd-error hover:bg-vd-border">Sign out</button>
                        </form>
                    </div>
                </details>
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
        Veridium Teknologi Solusi &mdash; Portal
    </footer>
</div>

</body>
</html>
