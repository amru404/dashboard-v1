<button {{ $attributes->merge(['type' => 'submit', 'class' => 'vd-btn-danger']) }}>
    {{ $slot }}
</button>
