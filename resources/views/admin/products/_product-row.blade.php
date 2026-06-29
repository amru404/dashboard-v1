{{-- Recursive product row component for displaying product hierarchy --}}
@foreach ($products as $product)
    @php
        $children = $product->subProducts;
        $licenseCount = $product->totalLicenseCount();
        $accordionId = "accordion-product-{$product->id}";
        $depthClass = 'ml-' . ($depth * 4);
    @endphp

    <div class="rounded-lg border border-[#2a3f5f] overflow-hidden bg-[#0f1829]/40">
        <!-- Product Header Row -->
        <div class="bg-[#0f1829]/30 p-4">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded border border-[#2a3f5f] bg-[#0f1829] flex items-center justify-center flex-shrink-0">
                            <span class="text-xs font-semibold text-gray-500">{{ $depth }}</span>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-200">{{ $product->name }}</h3>
                            @if ($product->code)
                                <p class="text-xs text-gray-500 font-mono">{{ $product->code }}</p>
                            @endif
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-purple-500/20 text-purple-300 border border-purple-500/30">
                            Level {{ $depth }}
                        </span>
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-500/20 text-blue-400 border border-blue-500/30">
                        {{ $licenseCount }}
                    </span>
                    @if ($product->is_active)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-500/20 text-green-400 border border-green-500/30">
                            Active
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-500/20 text-gray-400 border border-gray-500/30">
                            Inactive
                        </span>
                    @endif
                    <a 
                        href="{{ route('admin.products.edit', $product) }}" 
                        class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-semibold text-gray-300 hover:text-white hover:bg-[#1a3a52] transition-colors"
                        title="Edit product"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Nested Children -->
        @if ($children->isNotEmpty())
            <div class="border-t border-[#2a3f5f]">
                <button
                    @click="toggleAccordion('{{ $accordionId }}')"
                    class="w-full px-4 py-2.5 flex items-center justify-between hover:bg-[#0f1829]/20 transition-colors text-left"
                >
                    <div class="flex items-center gap-2">
                        <svg
                            class="w-4 h-4 text-gray-400 transition-transform flex-shrink-0"
                            :class="isExpanded('{{ $accordionId }}') ? 'rotate-90' : ''"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                        <span class="text-xs font-semibold text-gray-400">
                            {{ $children->count() }} child{{ $children->count() !== 1 ? 'ren' : '' }}
                        </span>
                    </div>
                </button>

                <div
                    x-show="isExpanded('{{ $accordionId }}')"
                    x-collapse
                    class="border-t border-[#2a3f5f] bg-[#0f1829]/50"
                >
                    <div class="p-4 space-y-3">
                        @include('admin.products._product-row', ['products' => $children, 'depth' => $depth + 1])
                    </div>
                </div>
            </div>
        @endif
    </div>
@endforeach
