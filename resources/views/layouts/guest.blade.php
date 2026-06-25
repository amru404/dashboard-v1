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
<body class="bg-vd-gradient text-vd-on-surface antialiased">

<div class="min-h-screen flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-5xl">
        {{-- ── Centered Authentication Card ── --}}
        <div class="grid grid-cols-1 lg:grid-cols-[38%_62%] gap-0 rounded-xl overflow-hidden shadow-vd-lg border border-vd-border">
            
            {{-- ── Left panel: branding ── --}}
            <section class="relative hidden lg:flex lg:flex-col lg:justify-between overflow-hidden bg-vd-secondary px-12 py-12">
                {{-- background glow --}}
                <div class="absolute inset-0 pointer-events-none vd-hero-glow"></div>
                {{-- neon lines decoration --}}
                <div class="absolute -top-32 -right-32 w-[600px] h-[600px] rounded-full border border-vd-tertiary/20 pointer-events-none"></div>
                <div class="absolute -top-20 -right-20 w-[400px] h-[400px] rounded-full border border-vd-accent-cyan/10 pointer-events-none"></div>

                {{-- Logo --}}
                <a href="/" class="relative z-10 inline-flex items-center gap-3">
                    <x-application-logo class="h-5 w-5 fill-current text-vd-primary" />
                </a>

                {{-- Branding content --}}
                <div class="relative z-10 max-w-sm">
                    <p class="text-eyebrow tracking-[0.18em] text-vd-primary uppercase mb-4">License Portal</p>
                    <h1 class="text-headline-lg text-vd-text leading-tight mb-5">Secure License Distribution</h1>
                    <p class="text-body-md text-vd-muted leading-relaxed">
                        A private software distribution backend for organizations and desktop installer verification.
                    </p>
                </div>

                {{-- Footnote card --}}
                <div class="relative z-10 rounded-lg border border-vd-border-strong bg-white/[0.04] p-4 text-body-sm text-vd-muted leading-relaxed">
                    Built for internal license management with secure access boundaries.
                </div>
            </section>

            {{-- ── Right panel: form ── --}}
            <main class="flex flex-col items-center justify-center px-8 py-12 lg:px-12 bg-vd-surface min-h-[500px] lg:min-h-auto">
                <div class="w-full max-w-sm">
                    {{-- Mobile logo --}}
                    <div class="mb-8 flex items-center gap-3 lg:hidden">
                        <x-application-logo class="h-6 w-6 fill-current text-vd-primary" />
                    </div>

                    {{-- Form content --}}
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>
</div>

</body>
</html>
