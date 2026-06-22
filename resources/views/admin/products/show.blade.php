@extends('layouts.admin')

@section('title', $product->name)

@section('content')
    <x-page-header title="{{ $product->name }}" subtitle="Product details and recursive child products.">
        <x-slot name="actions">
            <x-button variant="ghost" :href="route('admin.products.index')">Product tree</x-button>
            <x-button variant="secondary" :href="route('admin.products.edit', $product)">Edit product</x-button>
        </x-slot>
    </x-page-header>

    <x-product-breadcrumbs
        :breadcrumbs="$breadcrumbs"
        :current="$product"
        :path="$product->parent_id ? $catalogPath : null"
    />

    <div class="grid gap-6 lg:grid-cols-[1fr_0.7fr]">
        <x-card>
            <dl class="grid gap-5 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-semibold text-madani-muted">Product ID</dt>
                    <dd class="mt-1 font-mono text-base font-bold text-madani-deep">{{ $product->code }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-semibold text-madani-muted">Status</dt>
                    <dd class="mt-1"><x-badge :active="$product->is_active" /></dd>
                </div>
                <div>
                    <dt class="text-sm font-semibold text-madani-muted">Parent</dt>
                    <dd class="mt-1 text-base text-madani-deep">
                        @if ($product->parent)
                            <a href="{{ route('admin.products.show', $product->parent) }}" class="font-semibold text-madani-green hover:text-madani-deep">{{ $product->parent->name }}</a>
                        @else
                            Top-level product
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-semibold text-madani-muted">Direct children</dt>
                    <dd class="mt-1 text-base text-madani-deep">{{ $product->sub_products_count }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-sm font-semibold text-madani-muted">Description</dt>
                    <dd class="mt-1 whitespace-pre-line text-base leading-7 text-madani-deep">{{ $product->description ?? '-' }}</dd>
                </div>
            </dl>
        </x-card>

        <x-card>
            <p class="text-sm font-semibold text-madani-muted">Usage</p>
            <dl class="mt-5 space-y-4">
                <div class="flex justify-between gap-4">
                    <dt class="text-sm text-madani-muted">Licenses</dt>
                    <dd class="text-sm font-semibold text-madani-deep">{{ $product->licenses_count }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-sm text-madani-muted">Entitlements</dt>
                    <dd class="text-sm font-semibold text-madani-deep">{{ $product->entitlements_count }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-sm text-madani-muted">Download items</dt>
                    <dd class="text-sm font-semibold text-madani-deep">{{ $product->download_items_count }}</dd>
                </div>
            </dl>

            <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="mt-6" onsubmit="return confirm('Delete this product?')">
                @csrf
                @method('DELETE')
                <x-button variant="danger" class="gap-2">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M3 6h18" />
                        <path d="M8 6V4h8v2" />
                        <path d="M19 6l-1 14H6L5 6" />
                        <path d="M10 11v5" />
                        <path d="M14 11v5" />
                    </svg>
                    <span>Delete product</span>
                </x-button>
            </form>
            <p class="mt-3 text-xs leading-5 text-madani-muted">Products with child products or linked license data are protected from deletion.</p>
        </x-card>
    </div>

    <x-card class="mt-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-bold text-madani-deep">Child products</h2>
                <p class="mt-1 text-sm text-madani-muted">Recursive descendants under this product.</p>
            </div>
            <x-button :href="route('admin.products.create', ['parent_id' => $product->id])">Create child product</x-button>
        </div>

        <div class="mt-6">
            @if ($product->allChildren->isNotEmpty())
                <x-product-tree
                    :products="$product->allChildren"
                    :depth="$breadcrumbs->count()"
                    :parent-name="$product->name"
                    :parent-path="$catalogPath"
                    :show-context="true"
                />
            @else
                <p class="text-sm text-madani-muted">This product has no child products.</p>
            @endif
        </div>
    </x-card>
@endsection
