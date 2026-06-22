@props([
    'breadcrumbs',
    'current',
    'routeName' => 'admin.products.show',
    'linkItems' => true,
    'path' => null,
])

<nav aria-label="Product breadcrumb" {{ $attributes->merge(['class' => 'mb-6 rounded-2xl border border-madani-border bg-white px-5 py-4 shadow-sm']) }}>
    <ol class="flex flex-wrap items-center gap-2 text-sm">
        @foreach ($breadcrumbs as $breadcrumb)
            <li class="flex items-center gap-2">
                @if (! $loop->first)
                    <span class="text-madani-muted">/</span>
                @endif

                @if ($breadcrumb->is($current) || ! $linkItems)
                    <span class="font-bold text-madani-deep">{{ $breadcrumb->name }}</span>
                @else
                    <a href="{{ route($routeName, $breadcrumb) }}" class="font-semibold text-madani-green hover:text-madani-deep">
                        {{ $breadcrumb->name }}
                    </a>
                @endif
            </li>
        @endforeach
    </ol>

    @if ($path)
        <p class="mt-2 text-xs font-semibold uppercase tracking-[0.16em] text-madani-muted">{{ $path }}</p>
    @endif
</nav>
