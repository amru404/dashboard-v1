@props(['value' => null])

<label {{ $attributes->merge(['class' => 'block text-label-sm text-vd-on-surface/70']) }}>
    {{ $value ?? $slot }}
</label>
