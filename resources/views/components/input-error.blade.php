@props(['messages'])

@if ($messages)
    <p {{ $attributes->merge(['class' => 'mt-1 text-body-sm text-vd-error']) }}>
        {{ implode(', ', (array) $messages) }}
    </p>
@endif
