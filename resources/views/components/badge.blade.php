@props(['active' => true])

@php
    $classes = $active
        ? 'bg-madani-pale text-madani-green'
        : 'bg-gray-100 text-gray-500';
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold '.$classes]) }}>
    {{ $slot->isEmpty() ? ($active ? 'Active' : 'Inactive') : $slot }}
</span>
