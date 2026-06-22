@props(['value' => null])

<label {{ $attributes->merge(['class' => 'madani-label']) }}>
    {{ $value ?? $slot }}
</label>
