@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'vd-input disabled:cursor-not-allowed disabled:opacity-50']) }}>
