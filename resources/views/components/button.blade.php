@props([
    'variant'  => 'primary',
    'href'     => null,
    'type'     => 'submit',
    'disabled' => false,
])

@php
    $classes = match($variant) {
        'secondary' => 'vd-btn-secondary',
        'ghost'     => 'vd-btn-ghost',
        'danger',
        'critical'  => 'vd-btn-danger',
        default     => 'vd-btn-primary',
    };
@endphp

@if ($href && ! $disabled)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}"
        @disabled($disabled)
        {{ $attributes->merge(['class' => $classes . ' disabled:cursor-not-allowed disabled:opacity-50']) }}>
        {{ $slot }}
    </button>
@endif
