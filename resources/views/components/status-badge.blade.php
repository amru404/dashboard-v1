@props([
    'active' => null,
    'status' => null,
])

@php
    $label = $slot->isEmpty()
        ? ($status ? Str::headline((string) $status) : ((bool) $active ? 'Active' : 'Inactive'))
        : trim((string) $slot);

    $normalized = Str::lower((string) ($status ?? $label));

    $classes = match ($normalized) {
        'active', 'enabled', 'current', 'no expiry' => 'bg-madani-pale text-madani-green',
        'expired', 'blocked', 'danger', 'deleted' => 'bg-red-50 text-red-700',
        'suspended', 'inactive', 'disabled' => 'bg-gray-100 text-gray-500',
        'expiring soon', 'warning' => 'bg-amber-50 text-amber-700',
        default => ((bool) $active ? 'bg-madani-pale text-madani-green' : 'bg-gray-100 text-gray-500'),
    };
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold '.$classes]) }}>
    {{ $label }}
</span>
