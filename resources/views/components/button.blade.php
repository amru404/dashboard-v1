@props([
    'variant'  => 'primary',
    'href'     => null,
    'type'     => 'submit',
    'disabled' => false,
    'size'     => 'md',
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-semibold transition-all duration-200 rounded-lg disabled:cursor-not-allowed disabled:opacity-50';
    
    $sizeClasses = match($size) {
        'sm' => 'px-3 py-1.5 text-xs',
        'lg' => 'px-6 py-3 text-base',
        default => 'px-4 py-2.5 text-sm', // md
    };
    
    $variantClasses = match($variant) {
        'primary' => 'bg-vd-primary hover:bg-vd-primary/90 text-white shadow-sm hover:shadow-md',
        'secondary' => 'border border-vd-border hover:bg-white/5 text-gray-300 hover:text-white',
        'ghost' => 'text-gray-300 hover:text-white hover:bg-white/5',
        'danger' => 'bg-vd-error hover:bg-vd-error/90 text-white shadow-sm hover:shadow-md',
        'success' => 'bg-vd-success hover:bg-vd-success/90 text-white shadow-sm hover:shadow-md',
        'warning' => 'bg-vd-warning hover:bg-vd-warning/90 text-white shadow-sm hover:shadow-md',
        'cyan' => 'bg-vd-accent-cyan hover:bg-vd-accent-cyan/90 text-white shadow-sm hover:shadow-md',
        'magenta' => 'bg-vd-accent-magenta hover:bg-vd-accent-magenta/90 text-white shadow-sm hover:shadow-md',
        'outline-primary' => 'border-2 border-vd-primary text-vd-primary hover:bg-vd-primary hover:text-white',
        'outline-secondary' => 'border-2 border-gray-400 text-gray-300 hover:bg-gray-400 hover:text-gray-900',
        default => 'bg-vd-primary hover:bg-vd-primary/90 text-white shadow-sm hover:shadow-md',
    };
    
    $classes = "{$baseClasses} {$sizeClasses} {$variantClasses}";
@endphp

@if ($href && ! $disabled)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}"
        @disabled($disabled)
        {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
