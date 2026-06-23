@props(['active' => null, 'status' => null])

@php
    $label = $slot->isEmpty()
        ? ($status ? Str::headline((string) $status) : ((bool) $active ? 'Active' : 'Inactive'))
        : trim((string) $slot);

    $normalized = Str::lower((string) ($status ?? $label));

    $classes = match ($normalized) {
        'active', 'enabled', 'current', 'no expiry' => 'vd-chip-success',
        'expired', 'blocked', 'danger', 'deleted'   => 'vd-chip-error',
        'suspended', 'inactive', 'disabled'          => 'vd-chip',
        'expiring soon', 'warning'                   => 'vd-chip-warning',
        default => ((bool) $active ? 'vd-chip-success' : 'vd-chip'),
    };
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $label }}
</span>
