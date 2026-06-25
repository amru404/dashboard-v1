@props(['product', 'depth' => 0])

<div class="space-y-4" style="margin-left: {{ $depth * 1.5 }}rem;">
    <div class="rounded-3xl border border-vd-border bg-[#0f1829]/80 p-5 shadow-sm">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div class="min-w-0">
                <p class="text-sm font-semibold text-white truncate">{{ $product->name }}</p>
                <p class="text-xs text-vd-muted truncate">{{ $product->code }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center rounded-full border border-vd-border bg-vd-secondary/80 px-3 py-1 text-xs font-semibold text-vd-primary">
                    {{ $product->licenses->count() }} {{ $product->licenses->count() === 1 ? 'key' : 'keys' }}
                </span>
                <span class="inline-flex items-center rounded-full border border-vd-border bg-vd-secondary/70 px-3 py-1 text-xs text-vd-muted">
                    {{ $product->allChildren->count() }} sub-products
                </span>
            </div>
        </div>

        @if ($product->licenses->isNotEmpty())
            <div class="mt-5 space-y-4">
                @foreach ($product->licenses as $license)
                    <x-license-key-row :license="$license" />
                @endforeach
            </div>
        @endif
    </div>

        @foreach ($product->allChildren as $child)
        @include('user.licenses._product-tree-node', [
            'product' => $child,
            'depth' => $depth + 1,
        ])
    @endforeach
</div>
