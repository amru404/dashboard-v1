@props(['value'])

<label {{ $attributes->merge(['class' => 'madani-label']) }}>
    {{ $value ?? $slot }}
</label>
