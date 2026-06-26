@props([
    'subProducts',
    'depth' => 0,
])

<div class="space-y-3">
    @foreach ($subProducts as $subProduct)
        <div class="sub-product-item rounded-lg border border-[#2a3f5f] bg-[#0f1829]/40 p-4" 
             data-name="{{ strtolower($subProduct->name) }}" 
             data-code="{{ strtolower($subProduct->code) }}"
             data-status="{{ $subProduct->is_active ? 'active' : 'inactive' }}"
             style="@if($depth > 0) margin-left: {{ $depth * 1.5 }}rem; @endif">
            
            <div class="flex items-start justify-between gap-3 mb-3">
                <div class="flex-1 min-w-0">
                    <h3 class="text-base font-bold text-white mb-1">
                        @if($depth > 0)
                            <span class="text-gray-500 mr-2">{{ str_repeat('→ ', $depth) }}</span>
                        @endif
                        {{ $subProduct->name }}
                    </h3>
                    <p class="text-xs text-gray-400 font-mono">{{ $subProduct->code }}</p>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <x-badge :active="$subProduct->is_active">
                        {{ $subProduct->is_active ? 'Active' : 'Inactive' }}
                    </x-badge>
                </div>
            </div>

            {{-- Recursively show children --}}
            @if ($subProduct->allChildren->isNotEmpty())
                <div class="mt-3 pt-3 border-t border-[#2a3f5f]">
                    <x-product-sub-tree :subProducts="$subProduct->allChildren" :depth="$depth + 1" />
                </div>
            @endif
        </div>
    @endforeach
</div>
