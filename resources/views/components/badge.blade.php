@props(['active' => true, 'variant' => null])

@php
    if ($variant) {
        $classes = match($variant) {
            'success' => 'vd-chip-success',
            'warning' => 'vd-chip-warning',
            'error'   => 'vd-chip-error',
            'cyan'    => 'vd-chip-cyan',
            default   => 'vd-chip',
        };
    } else {
        $classes = $active ? 'vd-chip-success' : 'vd-chip-error';
    }
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot->isEmpty() ? ($active ? 'Active' : 'Inactive') : $slot }}
</span>
