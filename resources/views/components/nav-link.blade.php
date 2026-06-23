@props(['active'])

@php
    $classes = ($active ?? false) ? 'vd-nav-link-active' : 'vd-nav-link';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
