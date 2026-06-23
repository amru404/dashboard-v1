<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Customer Area') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Rethink+Sans:wght@400;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-vd-neutral text-vd-on-surface antialiased">

<div class="min-h-screen grid lg:grid-cols-[1.1fr_0.9fr]">

    {{-- ── Left panel: branding ── --}}
    <section class="relative hidden overflow-hidden bg-vd-secondary lg:flex lg:flex-col lg:justify-between px-12 py-12">
        {{-- background glow --}}
        <div class="absolute inset-0 pointer-events-none vd-hero-glow"></div>
        {{-- neon lines decoration --}}
        <div class="absolute -top-32 -right-32 w-[600px] h-[600px] rounded-full border border-vd-tertiary/20 pointer-events-none"></div>
        <div class="absolute -top-20 -right-20 w-[400px] h-[400px] rounded-full border border-vd-accent-cyan/10 pointer-events-none"></div>

        {{-- Logo --}}
        <a href="/" class="relative z-10 inline-flex items-center gap-3">
            <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-vd-primary/20 border border-vd-primary/40">
                <x-application-logo class="h-6 w-6 fill-current text-vd-primary" />
            </span>
            <span class="text-label-lg text-vd-on-surface tracking-wide">Customer Area</span>
        </a>

        {{-- Hero copy --}}
        <div class="relative z-10 max-w-md">
            <p class="text-eyebrow tracking-[0.18em] text-vd-muted uppercase mb-4">License Portal</p>
            <h1 class="text-display text-vd-text leading-tight mb-5">Secure License<br>Distribution</h1>
            <p class="text-body-lg text-vd-muted leading-relaxed">
                A private software distribution backend for organizations, users, license access, and desktop installer verification.
            </p>
        </div>

        {{-- Footnote card --}}
        <div class="relative z-10 rounded-lg border border-vd-border-strong bg-white/[0.04] p-5 text-body-sm text-vd-muted leading-relaxed">
            Built for internal license management with clean access boundaries for admins, customers, and desktop installers.
        </div>
    </section>

    {{-- ── Right panel: form ── --}}
    <main class="flex min-h-screen items-center justify-center px-6 py-12 bg-vd-neutral">
        <div class="w-full max-w-md">
            {{-- Mobile logo --}}
            <div class="mb-8 flex items-center gap-3 lg:hidden">
                <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-vd-primary/20 border border-vd-primary/40">
                    <x-application-logo class="h-6 w-6 fill-current text-vd-primary" />
                </span>
                <span class="text-label-lg text-vd-on-surface">Customer Area</span>
            </div>

            <div class="vd-card">
                {{ $slot }}
            </div>
        </div>
    </main>
</div>

</body>
</html>
