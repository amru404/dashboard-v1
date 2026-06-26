<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Portal') — {{ config('app.name', 'Veriotech') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Rethink+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('img/favicon.svg') }}">
    

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-vd-neutral text-vd-on-surface antialiased" style="background-image: linear-gradient(75deg, #030b15, #013169);">

<div class="min-h-screen flex flex-col">

    {{-- ─────────── Top nav ─────────── --}}
    <header class="sticky top-0 z-40 border-b border-vd-border bg-transparent backdrop-blur-md min-h-[80px]">
        <div class="mx-auto flex w-full max-w-screen-xl items-center px-4 py-3 sm:px-6 lg:px-8">

            {{-- Left: logo --}}
            <div class="flex items-center gap-6">
                <a href="{{ route('user.dashboard') }}" class="flex items-center gap-3 shrink-0">
                    <span>
                        <x-application-logo class="h-2 w-2 fill-current text-vd-primary" />
                    </span>
                </a>
            </div>

            {{-- Center: nav --}}
            <div class="flex-1 flex justify-center">
                <nav class="hidden items-center gap-1 md:flex">
                    <a href="{{ route('user.dashboard') }}"
                       class="{{ request()->routeIs('user.dashboard') ? 'vd-nav-link-active' : 'vd-nav-link' }}">
                        Overview
                    </a>
                    <a href="{{ route('user.products.index') }}"
                       class="{{ request()->routeIs('user.products.*') ? 'vd-nav-link-active' : 'vd-nav-link' }}">
                        Software Products
                    </a>
                    <a href="{{ route('user.licenses.index') }}"
                       class="{{ request()->routeIs('user.licenses.*') ? 'vd-nav-link-active' : 'vd-nav-link' }}">
                        License Keys
                    </a>
                    <a href="{{ route('user.downloads.index') }}"
                       class="{{ request()->routeIs('user.downloads.*') ? 'vd-nav-link-active' : 'vd-nav-link' }}">
                        Downloads
                    </a>
                </nav>
            </div>

            {{-- Right: profile dropdown --}}
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
                <a href="{{ route('user.dashboard') }}"
                   class="{{ request()->routeIs('user.dashboard') ? 'vd-nav-link-active' : 'vd-nav-link' }}">Dashboard</a>
                <a href="{{ route('user.products.index') }}"
                   class="{{ request()->routeIs('user.products.*') ? 'vd-nav-link-active' : 'vd-nav-link' }}">Products</a>
                <a href="{{ route('user.licenses.index') }}"
                   class="{{ request()->routeIs('user.licenses.*') ? 'vd-nav-link-active' : 'vd-nav-link' }}">Licenses</a>
                <a href="{{ route('user.downloads.index') }}"
                   class="{{ request()->routeIs('user.downloads.*') ? 'vd-nav-link-active' : 'vd-nav-link' }}">Downloads</a>
            </div>
        </div>
    </header>

    {{-- ─────────── Main ─────────── --}}
    <main class="flex-1 mx-auto w-full max-w-screen-xl px-4 py-8 sm:px-6 lg:px-8">
        <x-alert />
        @yield('content')
    </main>

    <footer class="border-t border-vd-border px-6 py-4 text-center text-[11px] text-vd-muted">
        Veridium Teknologi Solusi &mdash; Portal
    </footer>
</div>

</body>
</html>
