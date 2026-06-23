@props(['value', 'for' => null])

<label {{ $attributes->merge(['class' => 'block text-label-sm text-vd-on-surface/70 mb-1']) }}
    @if ($for) for="{{ $for }}" @endif>
    {{ $value ?? $slot }}
</label>
