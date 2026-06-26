@props([
    'products',
    'title' => 'Nested Product Tree',
    'description' => 'Products can be nested to unlimited depth. Use top-level products for families and child products for editions, modules, or installers.',
    'interactive' => true,
    'showFilters' => true,
])

<div class="vd-card border-[#2a3f5f]" x-data="productTreeManager">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between mb-5">
        <div>
            <h2 class="text-lg font-bold text-white">{{ $title }}</h2>
            <p class="mt-1 text-sm text-gray-300 leading-relaxed">
                {{ $description }}
            </p>
        </div>
    </div>

    @if ($products->isNotEmpty() && $showFilters)
        {{-- Filters --}}
        <div class="grid gap-4 rounded-lg border border-[#2a3f5f] bg-[#0f1829]/60 p-4
                    sm:grid-cols-2 lg:grid-cols-[1fr_180px_180px_auto] mb-6">
            <div>
                <label for="product_search" class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Search products</label>
                <input id="product_search" type="search" class="w-full px-3 py-2 bg-[#0f1829] border border-[#2a3f5f] rounded-lg text-white text-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-vd-primary focus:border-transparent"
                    placeholder="Name or code"
                    x-model.debounce.150ms="search">
            </div>
            <div>
                <label for="product_status_filter" class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Status</label>
                <select id="product_status_filter" class="w-full px-3 py-2 bg-[#0f1829] border border-[#2a3f5f] rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-vd-primary focus:border-transparent" x-model="status">
                    <option value="all">All statuses</option>
                    <option value="active">Active only</option>
                    <option value="inactive">Inactive only</option>
                </select>
            </div>
            <div>
                <label for="product_level_filter" class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Level</label>
                <select id="product_level_filter" class="w-full px-3 py-2 bg-[#0f1829] border border-[#2a3f5f] rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-vd-primary focus:border-transparent" x-model="level">
                    <option value="all">All Product</option>
                    <option value="top">Master Product</option>
                </select>
            </div>
            <div class="flex flex-wrap items-end gap-2">
                <button type="button"
                    class="inline-flex items-center px-3 py-2 rounded-lg bg-white/10 hover:bg-white/15 text-white text-sm border border-white/20 transition-colors"
                    @click="expandAll()">Expand all</button>
                <button type="button"
                    class="inline-flex items-center px-3 py-2 rounded-lg bg-white/10 hover:bg-white/15 text-white text-sm border border-white/20 transition-colors"
                    @click="collapseAll()">Collapse all</button>
                <button type="button"
                    class="inline-flex items-center px-3 py-2 text-sm text-gray-400 hover:text-white transition-colors"
                    @click="clearFilters()">Clear</button>
            </div>
        </div>
    @endif

    <div>
        @if ($products->isNotEmpty())
            <x-product-tree :products="$products" :interactive="$interactive" />
        @else
            <div class="rounded-lg border border-dashed border-[#2a3f5f] bg-[#0f1829]/30 p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <p class="mt-4 text-sm text-gray-400 mb-4">No products have been created yet.</p>
                {{ $emptySlot ?? '' }}
            </div>
        @endif
    </div>
</div>
