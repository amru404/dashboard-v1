@extends('layouts.admin')

@section('title', 'Products')

@section('content')

{{-- ── Page Header ── --}}
<div class="mb-8">
    <div class="flex items-start justify-between gap-4 mb-2">
        <div class="flex-1">
            <h1 class="text-3xl font-bold text-white mb-2">Products</h1>
            <p class="text-base text-gray-300">
                Build the recursive product catalog used by licenses, entitlements, and downloads.
            </p>
        </div>
        <div class="shrink-0">
            <a href="{{ route('admin.products.create') }}" class="inline-flex items-center px-4 py-2.5 rounded-lg bg-vd-primary hover:bg-vd-primary/90 text-white font-semibold text-sm transition-colors">
                Create Product
            </a>
        </div>
    </div>
</div>

<x-product-tree-section :products="$rootProducts" :interactive="true">
    @slot('emptySlot')
        <a href="{{ route('admin.products.create') }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-vd-primary hover:bg-vd-primary/90 text-white font-semibold text-sm transition-colors">
            Create First Product
        </a>
    @endslot
</x-product-tree-section>
@endsection
