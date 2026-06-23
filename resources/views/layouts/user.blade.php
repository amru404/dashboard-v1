<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Portal') — {{ config('app.name', 'Customer Area') }}</title>

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
                <a href="{{ route('user.dashboard') }}" class="flex items-center gap-3 shrink-0">
                    <span class="flex h-9 w-9 items-center justify-center rounded-md bg-vd-primary/20 border border-vd-primary/40">
                        <x-application-logo class="h-5 w-5 fill-current text-vd-primary" />
                    </span>
                    <span class="hidden sm:block">
                        <span class="block text-label-lg text-vd-on-surface leading-tight">Customer Area</span>
                        <span class="block text-[11px] text-vd-muted leading-tight">Customer Portal</span>
                    </span>
                </a>

                {{-- Desktop nav --}}
                <nav class="hidden items-center gap-1 md:flex">
                    <a href="{{ route('user.dashboard') }}"
                       class="{{ request()->routeIs('user.dashboard') ? 'vd-nav-link-active' : 'vd-nav-link' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('user.products.index') }}"
                       class="{{ request()->routeIs('user.products.*') ? 'vd-nav-link-active' : 'vd-nav-link' }}">
                        Products
                    </a>
                    <a href="{{ route('user.licenses.index') }}"
                       class="{{ request()->routeIs('user.licenses.*') ? 'vd-nav-link-active' : 'vd-nav-link' }}">
                        Licenses
                    </a>
                    <a href="{{ route('user.downloads.index') }}"
                       class="{{ request()->routeIs('user.downloads.*') ? 'vd-nav-link-active' : 'vd-nav-link' }}">
                        Downloads
                    </a>
                </nav>
            </div>

            {{-- Right side --}}
            <div class="flex items-center gap-3">
                <div class="hidden text-right sm:block">
                    <p class="text-label-sm text-vd-on-surface">{{ Auth::user()->name }}</p>
                    <p class="text-[11px] text-vd-muted">{{ Auth::user()->organization?->name ?? 'No organization' }}</p>
                </div>
                <a href="{{ route('profile.edit') }}" class="vd-btn-ghost hidden sm:inline-flex">Profile</a>
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
        Customer Area &mdash; Customer Portal
    </footer>
</div>

</body>
</html>
