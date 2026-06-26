@props(['product', 'depth' => 0])

@php
    // Filter: Only show products that have licenses (directly or in descendants)
    $hasLicenses = $product->licenses->isNotEmpty();
    $childrenWithLicenses = $product->allChildren->filter(function($child) {
        return $child->licenses->isNotEmpty() || 
               $child->allChildren->some(function($grandchild) {
                   return $grandchild->licenses->isNotEmpty();
               });
    });
    
    // Only render if this product or descendants have licenses
    $shouldRender = $hasLicenses || $childrenWithLicenses->isNotEmpty();
@endphp

@if ($shouldRender)
<div class="space-y-3">
    <div class="rounded-lg border border-[#2a3f5f] bg-[#0f1829]/40 p-4">
        <div class="flex items-start justify-between gap-3 mb-3">
            <div class="flex-1 min-w-0">
                <h3 class="text-sm font-semibold text-white mb-1">
                    @if($depth > 0)
                        <span class="text-gray-500 mr-2">{{ str_repeat('→ ', $depth) }}</span>
                    @endif
                    {{ $product->name }}
                </h3>
                <p class="text-xs text-gray-400 font-mono">{{ $product->code }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold bg-vd-primary/10 text-vd-primary border border-vd-primary/20">
                    {{ $product->licenses->count() }} {{ $product->licenses->count() === 1 ? 'key' : 'keys' }}
                </span>
                <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold bg-blue-500/10 text-blue-400 border border-blue-500/20">
                    {{ $product->totalActiveActivations() }} / {{ $product->totalMaxActivations() ?: '∞' }}
                </span>
            </div>
        </div>

        @if ($product->licenses->isNotEmpty())
            <div class="space-y-3 pt-3 border-t border-[#2a3f5f]">
                @foreach ($product->licenses as $license)
                    <div 
                        class="license-item rounded-lg border border-[#2a3f5f] bg-black/20 p-3"
                        x-data="{ show: false }"
                        data-license-key="{{ strtolower($license->license_key) }}"
                        data-license-status="{{ $license->isExpired() ? 'expired' : 'active' }}"
                        data-license-product="{{ strtolower($product->parent ? $product->parent->name : $product->name) }}"
                        data-license-subproduct="{{ strtolower($product->name) }}"
                    >
                        <div class="grid gap-3 lg:grid-cols-[2fr_1fr_1fr_auto] items-center">
                            {{-- License Key --}}
                            <div>
                                <p class="text-xs text-gray-400 mb-2">Key</p>
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 font-mono text-xs bg-black/30 rounded px-2 py-1.5 border border-[#2a3f5f]">
                                        <span x-show="!show" class="text-gray-400">••••-••••-••••</span>
                                        <span x-show="show" class="text-white break-all">{{ $license->license_key }}</span>
                                    </div>
                                    <button 
                                        @click="show = !show"
                                        class="flex-shrink-0 inline-flex items-center justify-center w-7 h-7 rounded bg-white/10 hover:bg-white/15 border border-white/20 transition-colors text-gray-300 hover:text-white"
                                    >
                                        <svg x-show="!show" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <svg x-show="show" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="display: none;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                        </svg>
                                    </button>
                                </div>
                                <p class="text-xs text-gray-400 mt-1">{{ $license->licenseType->name }}</p>
                            </div>

                            {{-- Status --}}
                            <div>
                                <p class="text-xs text-gray-400 mb-2">Status</p>
                                @if ($license->isExpired())
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-red-500/20 text-red-400 border border-red-500/30">
                                        Expired
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-green-500/20 text-green-400 border border-green-500/30">
                                        Active
                                    </span>
                                @endif
                            </div>

                            {{-- Activations --}}
                            <div>
                                <p class="text-xs text-gray-400 mb-2">Activations</p>
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-blue-500/20 text-blue-400 border border-blue-500/30">
                                    {{ $license->activeActivationCount() }} / {{ $license->max_activations ?? '∞' }}
                                </span>
                            </div>

                            {{-- Action --}}
                            <div>
                                <a href="{{ route('user.licenses.show', $license) }}" class="inline-flex items-center px-3 py-1.5 rounded bg-vd-primary/20 hover:bg-vd-primary/30 text-vd-primary font-semibold text-xs border border-vd-primary/30 transition-colors whitespace-nowrap">
                                    Details
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    @if ($childrenWithLicenses->isNotEmpty())
        <div class="pl-4 border-l-2 border-[#2a3f5f] space-y-3">
            @foreach ($product->allChildren as $child)
                @include('user.licenses._product-tree-node', [
                    'product' => $child,
                    'depth' => $depth + 1,
                ])
            @endforeach
        </div>
    @endif
</div>
@endif
