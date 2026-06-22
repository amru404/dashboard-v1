@extends('layouts.admin')

@section('title', 'Products')

@section('content')
    <x-page-header
        title="Products"
        subtitle="Build the recursive product catalog used by licenses, entitlements, and downloads."
    >
        <x-slot name="actions">
            <x-button :href="route('admin.products.create')">Create product</x-button>
        </x-slot>
    </x-page-header>

    <x-card x-data="productTreeManager">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h2 class="text-lg font-bold text-madani-deep">Nested product tree</h2>
                <p class="mt-2 text-sm leading-6 text-madani-muted">
                    Products can be nested to unlimited depth. Use top-level products for families and child products for editions, modules, or installers.
                </p>
            </div>
        </div>

        @if ($rootProducts->isNotEmpty())
            <div class="mt-6 grid gap-3 rounded-2xl border border-madani-border bg-madani-ghost p-4 lg:grid-cols-[1fr_180px_180px_auto]">
                <div>
                    <x-form-label for="product_search" value="Search products" />
                    <input
                        id="product_search"
                        type="search"
                        class="madani-input mt-2"
                        placeholder="Search by name or code"
                        x-model.debounce.150ms="search"
                    >
                </div>

                <div>
                    <x-form-label for="product_status_filter" value="Status" />
                    <select id="product_status_filter" class="madani-input mt-2" x-model="status">
                        <option value="all">All statuses</option>
                        <option value="active">Active only</option>
                        <option value="inactive">Inactive only</option>
                    </select>
                </div>

                <div>
                    <x-form-label for="product_level_filter" value="Level" />
                    <select id="product_level_filter" class="madani-input mt-2" x-model="level">
                        <option value="all">All levels</option>
                        <option value="top">Top-level only</option>
                        <option value="child">Child products</option>
                    </select>
                </div>

                <div class="flex flex-wrap items-end gap-2">
                    <button type="button" class="rounded-lg border border-madani-border bg-white px-3 py-2 text-sm font-semibold text-madani-deep transition hover:border-madani-green hover:text-madani-green madani-focus" @click="expandAll()">
                        Expand all
                    </button>
                    <button type="button" class="rounded-lg border border-madani-border bg-white px-3 py-2 text-sm font-semibold text-madani-deep transition hover:border-madani-green hover:text-madani-green madani-focus" @click="collapseAll()">
                        Collapse all
                    </button>
                    <button type="button" class="rounded-lg px-3 py-2 text-sm font-semibold text-madani-muted transition hover:bg-white hover:text-madani-deep madani-focus" @click="clearFilters()">
                        Clear
                    </button>
                </div>
            </div>
        @endif

        <div class="mt-6">
            @if ($rootProducts->isNotEmpty())
                <x-product-tree :products="$rootProducts" :interactive="true" />
            @else
                <div class="rounded-xl border border-dashed border-madani-border bg-madani-ghost p-8 text-center">
                    <p class="text-sm text-madani-muted">No products have been created.</p>
                    <x-button class="mt-4" :href="route('admin.products.create')">Create first product</x-button>
                </div>
            @endif
        </div>
    </x-card>
@endsection
