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
                <span class="inline-flex items-center rounded-full border border-vd-border bg-[#102836] px-3 py-1 text-xs font-semibold text-vd-primary">
                    {{ $product->totalActiveActivations() }} / {{ $product->totalMaxActivations() ?: 0 }} activations
                </span>
                <span class="inline-flex items-center rounded-full border border-vd-border bg-vd-secondary/70 px-3 py-1 text-xs text-vd-muted">
                    {{ $product->allChildren->count() }} sub-products
                </span>
            </div>
        </div>

        @if ($product->licenses->isNotEmpty())
            <div class="mt-5 space-y-4">
                <div class="rounded-3xl border border-vd-border bg-[#0f1829]/80 p-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-white">{{ $product->name }} licenses</p>
                            <p class="text-xs text-vd-muted">{{ $product->code }}</p>
                        </div>
                        <span class="inline-flex items-center rounded-full border border-vd-border bg-vd-secondary/80 px-3 py-1 text-xs font-semibold text-vd-primary">
                            {{ $product->licenses->count() }} {{ Str::plural('key', $product->licenses->count()) }}
                        </span>
                    </div>
                    <div class="mt-4 space-y-3 divide-y divide-vd-border">
                        @foreach ($product->licenses as $license)
                            <div class="grid gap-4 py-4 lg:grid-cols-[1.5fr_1fr_0.9fr] items-center">
                                <div class="rounded-2xl border border-vd-border bg-black/10 p-4 font-mono text-sm break-all">
                                    <p class="text-xs uppercase tracking-[0.2em] text-vd-muted mb-2">Key</p>
                                    <p class="text-white">{{ $license->masked_license_key }}</p>
                                    <p class="mt-2 text-xs text-vd-muted">Type: {{ $license->licenseType->name }}</p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-xs uppercase tracking-[0.2em] text-vd-muted">Status</p>
                                    <p class="font-semibold {{ $license->isExpired() ? 'text-red-400' : 'text-green-300' }}">
                                        {{ $license->isExpired() ? 'Expired' : 'Active' }}
                                    </p>
                                    <p class="text-xs text-vd-muted">
                                        Expires: {{ $license->expired_date?->format('M j, Y') ?? 'Never' }}
                                    </p>
                                </div>
                                <div class="space-y-2 text-right sm:text-left">
                                    <div class="text-xs uppercase tracking-[0.2em] text-vd-muted">Activations</div>
                                    <div class="inline-flex items-center gap-2 rounded-full border border-vd-border bg-vd-secondary/80 px-3 py-1 text-xs font-semibold text-vd-on-surface">
                                        {{ $license->activeActivationCount() }} / {{ $license->max_activations ?? '∞' }}
                                    </div>
                                    <a href="{{ route('user.licenses.show', $license) }}" class="inline-flex items-center rounded-full border border-vd-border bg-white/5 px-3 py-1 text-xs font-semibold text-vd-primary hover:bg-white/10">
                                        Details
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
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
