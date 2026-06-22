@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block rounded-lg bg-madani-pale px-4 py-2 text-start text-sm font-semibold text-madani-deep transition duration-150 ease-in-out'
            : 'block rounded-lg px-4 py-2 text-start text-sm font-semibold text-madani-muted transition duration-150 ease-in-out hover:bg-madani-pale hover:text-madani-deep';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
