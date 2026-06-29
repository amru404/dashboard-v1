@extends('layouts.admin')

@section('title', 'Products')

@section('content')

    <x-page-header title="Products" subtitle="Build the recursive product catalog used by licenses, entitlements, and downloads.">
        <x-slot name="actions">
            <x-button variant="primary" :href="route('admin.products.create')">Create Product</x-button>
        </x-slot>
    </x-page-header>

    <div class="space-y-4" x-data="productAccordion">
        @forelse ($rootProducts as $product)
            @php
                $directChildren = $product->subProducts;
                $totalLicenses = $product->totalLicenseCount();
                $accordionId = "accordion-product-{$product->id}";
            @endphp

            <!-- Product Row -->
            <div class="rounded-lg border border-[#2a3f5f] overflow-hidden">
                <!-- Product Header -->
                <div class="bg-gradient-to-r from-[#0f1829]/50 to-[#1a3a52]/30 border-b border-[#2a3f5f] p-4">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-vd-primary/20 flex items-center justify-center flex-shrink-0">
                                    <span class="text-sm font-bold text-vd-primary">
                                        @if ($product->is_active)
                                            ✓
                                        @else
                                            ✕
                                        @endif
                                    </span>
                                </div>
                                <div>
                                    <h2 class="text-lg font-semibold text-white">{{ $product->name }}</h2>
                                    @if ($product->code)
                                        <p class="text-sm text-gray-400">{{ $product->code }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-500/20 text-blue-400 border border-blue-500/30">
                                {{ $directChildren->count() }} sub-product{{ $directChildren->count() !== 1 ? 's' : '' }}
                            </span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-500/20 text-green-400 border border-green-500/30">
                                {{ $totalLicenses }} license{{ $totalLicenses !== 1 ? 's' : '' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Product Details Table -->
                <div class="bg-[#0f1829]/20 p-4">
                    <div class="grid grid-cols-4 gap-4 mb-4">
                        <!-- Description -->
                        <div class="col-span-2">
                            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Description</p>
                            <p class="text-sm text-gray-300">
                                @if ($product->description)
                                    {{ Str::limit($product->description, 100) }}
                                @else
                                    <span class="text-gray-500 italic">No description</span>
                                @endif
                            </p>
                        </div>

                        <!-- Status -->
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Status</p>
                            @if ($product->is_active)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-500/20 text-green-400 border border-green-500/30">
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-500/20 text-gray-400 border border-gray-500/30">
                                    Inactive
                                </span>
                            @endif
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-end gap-2">
                            <a 
                                href="{{ route('admin.products.edit', $product) }}" 
                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-vd-primary/20 text-vd-primary hover:bg-vd-primary/30 transition-colors text-xs font-medium"
                                title="Edit product"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Sub-products Accordion Toggle -->
                @if ($directChildren->isNotEmpty())
                    <div class="border-t border-[#2a3f5f] bg-[#0f1829]/50">
                        <button
                            @click="toggleAccordion('{{ $accordionId }}')"
                            class="w-full px-4 py-3 flex items-center justify-between hover:bg-[#0f1829]/40 transition-colors"
                        >
                            <div class="flex items-center gap-2">
                                <svg
                                    class="w-5 h-5 text-gray-400 transition-transform flex-shrink-0"
                                    :class="isExpanded('{{ $accordionId }}') ? 'rotate-90' : ''"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                    stroke-width="2"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                                <span class="text-sm font-semibold text-gray-300">View Sub-products</span>
                            </div>
                            <span class="text-xs text-gray-400">{{ $directChildren->count() }} items</span>
                        </button>

                        <!-- Sub-products Nested Content -->
                        <div
                            x-show="isExpanded('{{ $accordionId }}')"
                            x-collapse
                            class="bg-[#0f1829]/30 border-t border-[#2a3f5f]"
                        >
                            <div class="p-4 space-y-3">
                                @include('admin.products._product-row', ['products' => $directChildren, 'depth' => 1])
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <div class="rounded-lg border border-dashed border-[#2a3f5f] bg-[#0f1829]/30 p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m0 0l8 4m0 0l8-4m0 6l-8 4m0 0l-8-4m0 0l8 4m0 0l8-4M9 7v10m6 0V7"/>
                </svg>
                <p class="mt-4 text-sm text-gray-400">No products have been created yet.</p>
                <a href="{{ route('admin.products.create') }}" class="mt-4 inline-flex items-center px-4 py-2 rounded-lg bg-vd-primary hover:bg-vd-primary/90 text-white font-semibold text-sm transition-colors">
                    Create First Product
                </a>
            </div>
        @endforelse
    </div>

    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('productAccordion', () => ({
            expandedItems: [],

            toggleAccordion(id) {
                const index = this.expandedItems.indexOf(id);
                if (index > -1) {
                    this.expandedItems.splice(index, 1);
                } else {
                    this.expandedItems.push(id);
                }
            },

            isExpanded(id) {
                return this.expandedItems.includes(id);
            }
        }));
    });
    </script>

@endsection
