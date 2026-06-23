@props([
    'license',
    'revealUrl',
    'buttonId' => 'reveal-license-key',
])

<div {{ $attributes->merge(['class' => 'flex flex-col gap-3 rounded-lg border border-vd-border-strong bg-vd-secondary px-4 py-3 sm:flex-row sm:items-center sm:justify-between']) }}>
    <span class="font-mono text-body-sm text-vd-on-surface break-all">{{ $license->masked_license_key }}</span>
    @if (! $license->is_parent_only)
        <button
            id="{{ $buttonId }}"
            type="button"
            data-url="{{ $revealUrl }}"
            class="vd-btn-secondary shrink-0 text-body-sm">
            Reveal Key
        </button>
    @endif
</div>
