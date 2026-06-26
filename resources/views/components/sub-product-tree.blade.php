@props([
    'product',
    'depth' => 0,
])

@php
    $hasChildren = $product->allChildren->isNotEmpty();
    $paddingClass = match($depth) {
        0 => '',
        1 => 'ml-4 border-l-2 border-[#2a3f5f] pl-4',
        default => 'ml-4 border-l-2 border-[#2a3f5f] pl-4',
    };
@endphp

<div class="sub-product-item rounded-lg border border-[#2a3f5f] bg-[#0f1829]/40 p-4 {{ $paddingClass }}" 
     data-name="{{ strtolower($product->name) }}" 
     data-code="{{ strtolower($product->code) }}"
     data-status="{{ $product->is_active ? 'active' : 'inactive' }}">
    <div class="flex items-start justify-between gap-3">
        <div class="flex-1">
            <h3 class="text-base font-bold text-white mb-1">{{ $product->name }}</h3>
            <p class="text-xs text-gray-400 font-mono">{{ $product->code }}</p>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
            <x-badge :active="$product->is_active">
                {{ $product->is_active ? 'Active' : 'Inactive' }}
            </x-badge>
        </div>
    </div>
</div>

{{-- Render children recursively --}}
@if ($hasChildren)
    <div class="space-y-4 mt-4">
        @foreach ($product->allChildren as $child)
            <x-sub-product-tree :product="$child" :depth="$depth + 1" />
        @endforeach
    </div>
@endif
