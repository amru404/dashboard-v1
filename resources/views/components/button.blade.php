@props([
    'variant' => 'primary',
    'href' => null,
    'type' => 'submit',
    'disabled' => false,
])

@php
    $classes = [
        'primary' => 'inline-flex items-center justify-center rounded-lg bg-madani-success px-5 py-3 text-sm font-semibold text-white transition hover:bg-madani-green active:bg-madani-deep madani-focus',
        'secondary' => 'inline-flex items-center justify-center rounded-lg border border-madani-deep px-5 py-3 text-sm font-semibold text-madani-deep transition hover:bg-madani-deep hover:text-white madani-focus',
        'ghost' => 'inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-semibold text-madani-deep transition hover:bg-madani-pale madani-focus',
        'danger' => 'inline-flex items-center justify-center rounded-lg border border-red-300 bg-white px-5 py-3 text-sm font-semibold text-red-700 transition hover:border-red-500 hover:bg-red-50 madani-focus',
        'critical' => 'inline-flex items-center justify-center rounded-lg bg-red-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-red-700 madani-focus',
    ][$variant] ?? '';
@endphp

@if ($href && ! $disabled)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" @disabled($disabled) {{ $attributes->merge(['class' => $classes.' disabled:cursor-not-allowed disabled:opacity-60']) }}>
        {{ $slot }}
    </button>
@endif
