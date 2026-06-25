@props(['value', 'for' => null])

<label {{ $attributes->merge(['class' => 'block text-label-md text-vd-on-surface font-medium']) }}
    @if ($for) for="{{ $for }}" @endif>
    {{ $value ?? $slot }}
</label>
