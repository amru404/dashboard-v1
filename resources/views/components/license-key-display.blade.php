@props([
    'license',
    'revealUrl',
    'buttonId' => 'reveal-license-key',
])

<div {{ $attributes->merge(['class' => 'flex flex-col gap-3 rounded-xl border border-madani-border bg-madani-ghost px-4 py-3 sm:flex-row sm:items-center sm:justify-between']) }}>
    <span class="font-mono text-sm font-semibold text-madani-deep">{{ $license->masked_license_key }}</span>
    <button
        id="{{ $buttonId }}"
        type="button"
        data-url="{{ $revealUrl }}"
        class="inline-flex items-center justify-center rounded-lg border border-madani-deep px-4 py-2 text-sm font-semibold text-madani-deep transition hover:bg-madani-deep hover:text-white"
    >
        Reveal key
    </button>
</div>
