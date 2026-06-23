@extends('layouts.admin')

@section('title', 'Products')

@section('content')
    <x-page-header
        title="Products"
        subtitle="Build the recursive product catalog used by licenses, entitlements, and downloads."
    >
        <x-slot name="actions">
            <x-button :href="route('admin.products.create')">Create Product</x-button>
        </x-slot>
    </x-page-header>

    <div class="vd-card" x-data="productTreeManager">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between mb-5">
            <div>
                <h2 class="text-label-lg text-vd-on-surface">Nested Product Tree</h2>
                <p class="mt-1 text-body-sm text-vd-muted leading-relaxed">
                    Products can be nested to unlimited depth. Use top-level products for families and child products for editions, modules, or installers.
                </p>
            </div>
        </div>

        @if ($rootProducts->isNotEmpty())
            {{-- Filters --}}
            <div class="grid gap-4 rounded-lg border border-vd-border bg-vd-secondary/60 p-4
                        sm:grid-cols-2 lg:grid-cols-[1fr_180px_180px_auto] mb-6">
                <div>
                    <x-form-label for="product_search" value="Search products" />
                    <input id="product_search" type="search" class="vd-input mt-1"
                        placeholder="Name or code"
                        x-model.debounce.150ms="search">
                </div>
                <div>
                    <x-form-label for="product_status_filter" value="Status" />
                    <select id="product_status_filter" class="vd-input mt-1" x-model="status">
                        <option value="all">All statuses</option>
                        <option value="active">Active only</option>
                        <option value="inactive">Inactive only</option>
                    </select>
                </div>
                <div>
                    <x-form-label for="product_level_filter" value="Level" />
                    <select id="product_level_filter" class="vd-input mt-1" x-model="level">
                        <option value="all">All levels</option>
                        <option value="top">Top-level only</option>
                        <option value="child">Child products</option>
                    </select>
                </div>
                <div class="flex flex-wrap items-end gap-2">
                    <button type="button"
                        class="vd-btn-ghost border border-vd-border-strong text-body-sm"
                        @click="expandAll()">Expand all</button>
                    <button type="button"
                        class="vd-btn-ghost border border-vd-border-strong text-body-sm"
                        @click="collapseAll()">Collapse all</button>
                    <button type="button"
                        class="vd-btn-ghost text-body-sm text-vd-muted"
                        @click="clearFilters()">Clear</button>
                </div>
            </div>
        @endif

        <div>
            @if ($rootProducts->isNotEmpty())
                <x-product-tree :products="$rootProducts" :interactive="true" />
            @else
                <div class="rounded-lg border border-dashed border-vd-border-strong bg-vd-secondary/30 p-12 text-center">
                    <p class="text-body-sm text-vd-muted mb-4">No products have been created yet.</p>
                    <x-button :href="route('admin.products.create')">Create First Product</x-button>
                </div>
            @endif
        </div>
    </div>
@endsection
