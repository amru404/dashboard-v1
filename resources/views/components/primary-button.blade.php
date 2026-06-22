<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center rounded-lg bg-madani-success px-5 py-3 text-sm font-semibold text-white transition hover:bg-madani-green active:bg-madani-deep madani-focus']) }}>
    {{ $slot }}
</button>
