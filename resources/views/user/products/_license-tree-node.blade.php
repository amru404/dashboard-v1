@php
    $productLicenseItems = $licensesByProduct[$product->id] ?? collect();
@endphp

<div class="space-y-3">
    <div class="rounded-xl border border-[#2a3f5f] bg-[#0f1829]/40 p-4" style="margin-left: {{ $depth * 1.25 }}rem;">
        <div class="flex items-start justify-between gap-4 mb-3">
            <div>
                <h3 class="text-sm font-semibold text-white">{{ $product->name }}</h3>
                <p class="text-xs text-gray-400">{{ $product->code }}</p>
            </div>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-vd-primary/15 text-vd-primary border border-vd-primary/25">
                {{ $productLicenseItems->count() }} key{{ $productLicenseItems->count() === 1 ? '' : 's' }}
            </span>
        </div>

        @if ($productLicenseItems->isNotEmpty())
            <div class="space-y-3">
                @foreach ($productLicenseItems as $license)
                    <div x-data="{ show: false }" class="rounded-lg border border-[#2a3f5f] bg-[#10203a]/70 p-3">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-white">{{ $license->licenseType->name }}</p>
                                <p class="text-xs text-gray-400">Expires: {{ $license->expired_date?->format('M j, Y') ?? 'Never' }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-400">{{ $license->activations->count() }} / {{ $license->max_activations ?? '∞' }} activations</span>
                            </div>
                        </div>

                        <div class="mt-3 grid gap-3 sm:grid-cols-[1fr_auto] items-center">
                            <div class="font-mono text-sm bg-black/30 rounded px-3 py-2 border border-[#2a3f5f]">
                                <span x-show="!show" class="text-gray-400">{{ $license->masked_license_key }}</span>
                                <span x-show="show" class="text-white" x-text="'{{ $license->license_key }}'"></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <button
                                    type="button"
                                    @click="show = !show"
                                    class="inline-flex items-center gap-2 rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-xs font-semibold text-white transition hover:bg-white/10"
                                >
                                    <span x-text="show ? 'Hide key' : 'Show key'"></span>
                                </button>
                                <span class="inline-flex items-center px-2 py-1 rounded-full bg-slate-800/70 text-xs text-gray-300 border border-slate-700">
                                    {{ $license->sub_product_id ? 'Sub-product' : 'Parent' }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    @foreach ($product->subProducts as $childProduct)
        @if ($childProduct->subProducts->isNotEmpty() || ! empty($licensesByProduct[$childProduct->id]))
            @include('user.products._license-tree-node', [
                'product' => $childProduct,
                'licensesByProduct' => $licensesByProduct,
                'depth' => $depth + 1,
            ])
        @endif
    @endforeach
</div>
