<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center rounded-lg border border-madani-deep bg-white px-5 py-3 text-sm font-semibold text-madani-deep transition hover:bg-madani-deep hover:text-white madani-focus disabled:opacity-25']) }}>
    {{ $slot }}
</button>
