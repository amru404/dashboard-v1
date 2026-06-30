@extends('layouts.admin')

@section('title', 'Download Items')

@section('content')

@php
    $totalCount = $downloadItems->total();
    $activeCount = $downloadItems->where('is_active', true)->where('expired_date', null)->count() + 
                   $downloadItems->where('is_active', true)->where('expired_date', '>', now())->count();
    $totalSize = $downloadItems->sum('file_size');
    $products = $downloadItems->pluck('product')->unique('id');
@endphp

{{-- ── Page Header ── --}}
<div class="mb-8">
    <div class="flex items-start justify-between gap-4 mb-6">
        <div class="flex-1">
            <h1 class="text-3xl font-bold text-white mb-2">Download Items</h1>
            <p class="text-base text-gray-300">
                Manage downloadable files for entitled customers
            </p>
        </div>
        
        {{-- Layout Switcher & Create Button --}}
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-2 rounded-lg border border-[#2a3f5f] bg-[#0f1829] p-1">
                <button data-layout="grid" 
                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg transition-colors bg-vd-primary text-white"
                        title="Grid view">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4h6v6H4V4zm10 0h6v6h-6V4zM4 14h6v6H4v-6zm10 0h6v6h-6v-6z" />
                    </svg>
                </button>
                <button data-layout="list"
                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg transition-colors text-gray-400 hover:bg-white/5 hover:text-white"
                        title="List view">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 5h16M4 12h16M4 19h16" />
                    </svg>
                </button>
            </div>
            
            <x-button :href="route('admin.download-items.create')" variant="primary">
                New Download
            </x-button>
        </div>
    </div>

    {{-- Search & Filter Bar --}}
    <x-card>
        <div class="grid gap-3 sm:grid-cols-[1fr_auto_auto]">
            <div class="relative">
                <input 
                    type="text" 
                    id="downloadSearch"
                    placeholder="Search by filename or product..."
                    class="w-full px-4 py-2.5 pl-10 rounded-lg bg-[#0f1829] border border-[#2a3f5f] text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-vd-primary/50 focus:border-vd-primary transition-colors text-sm" />
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <select 
                id="downloadProductFilter" 
                class="px-4 py-2.5 rounded-lg bg-[#0f1829] border border-[#2a3f5f] text-white focus:outline-none focus:ring-2 focus:ring-vd-primary/50 focus:border-vd-primary transition-colors text-sm min-w-[160px]">
                <option value="all">All Products</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </select>
            
            <select 
                id="downloadStatusFilter" 
                class="px-4 py-2.5 rounded-lg bg-[#0f1829] border border-[#2a3f5f] text-white focus:outline-none focus:ring-2 focus:ring-vd-primary/50 focus:border-vd-primary transition-colors text-sm min-w-[140px]">
                <option value="all">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="expired">Expired</option>
            </select>
        </div>
    </x-car>
</div>

{{-- ── Stats Cards ── --}}
<div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
    <div class="vd-card border-[#2a3f5f] !p-5">
        <p class="text-xs text-gray-400 uppercase tracking-wider mb-2">Total Files</p>
        <p class="text-3xl font-bold text-white" id="downloadCount">{{ $totalCount }}</p>
        <p class="text-xs text-gray-500 mt-1"><span id="downloadLabel">{{ $totalCount === 1 ? 'item' : 'items' }}</span></p>
    </div>
    
    <div class="vd-card border-[#2a3f5f] !p-5">
        <p class="text-xs text-gray-400 uppercase tracking-wider mb-2">Active</p>
        <p class="text-3xl font-bold text-vd-primary">{{ $activeCount }}</p>
    </div>
    
    <div class="vd-card border-[#2a3f5f] !p-5">
        <p class="text-xs text-gray-400 uppercase tracking-wider mb-2">Total Size</p>
        <p class="text-3xl font-bold text-white">
            {{ number_format($totalSize / 1024 / 1024, 0) }} MB
        </p>
    </div>
    
    <div class="vd-card border-[#2a3f5f] !p-5">
        <p class="text-xs text-gray-400 uppercase tracking-wider mb-2">Products</p>
        <p class="text-3xl font-bold text-white">{{ $products->count() }}</p>
    </div>
</div>

{{-- ── Grid View ── --}}
<div id="gridView" class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
    @forelse ($downloadItems as $downloadItem)
        @php
            $status = $downloadItem->isExpired() ? 'expired' : ($downloadItem->is_active ? 'active' : 'inactive');
        @endphp
        <div class="download-item vd-card border-[#2a3f5f] !p-5"
             data-filename="{{ strtolower($downloadItem->file_name) }}"
             data-product="{{ strtolower($downloadItem->product->name) }}"
             data-product-id="{{ $downloadItem->product_id }}"
             data-status="{{ $status }}">
            
            <div class="flex items-start justify-between gap-3 mb-4">
                <h3 class="text-base font-bold text-white flex-1">{{ $downloadItem->file_name }}</h3>
                @if ($status === 'expired')
                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-red-500/20 text-red-400 border border-red-500/30">Expired</span>
                @elseif ($status === 'active')
                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-green-500/20 text-green-400 border border-green-500/30">Active</span>
                @else
                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-gray-500/20 text-gray-400 border border-gray-500/30">Inactive</span>
                @endif
            </div>
            
            <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-3">{{ $downloadItem->product->name }}</p>
            
            <div class="grid grid-cols-2 gap-3 text-sm mb-4">
                <div>
                    <p class="text-xs text-gray-400 mb-1">Version</p>
                    <p class="text-white font-medium">{{ $downloadItem->version ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Size</p>
                    <p class="text-white font-medium">{{ number_format($downloadItem->file_size / 1024 / 1024, 2) }} MB</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Expires</p>
                    <p class="text-white font-medium">{{ $downloadItem->expired_date?->format('M j, Y') ?? 'Never' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Downloads</p>
                    <p class="text-white font-medium">{{ $downloadItem->logs_count }}</p>
                </div>
            </div>
            
            <div class="flex items-center justify-center gap-6 pt-3 border-t border-[#2a3f5f]">
                <a href="{{ route('admin.download-items.show', $downloadItem) }}" class="text-xs font-semibold text-gray-300 hover:text-white">View</a>
                <a  href="{{ route('admin.download-items.edit', $downloadItem) }}" class="text-xs font-semibold text-gray-300 hover:text-white">Edit</a>

                <a href="#"
                onclick="event.preventDefault(); if(confirm('Delete this download item?')) document.getElementById('delete-{{ $downloadItem->id }}').submit();"
                class="text-xs font-semibold text-red-400 hover:text-red-300">
                    Delete
                </a>

                <form id="delete-{{ $downloadItem->id }}"
                    method="POST"
                    action="{{ route('admin.download-items.destroy', $downloadItem) }}"
                    class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    @empty
        <div class="vd-card border-[#2a3f5f] md:col-span-2 xl:col-span-3 !p-12 text-center">
            <svg class="mx-auto mb-4 h-12 w-12 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
            </svg>
            <p class="text-gray-400">No downloads available</p>
        </div>
    @endforelse
</div>

{{-- ── List View ── --}}
<div id="listView" class="hidden vd-card border-[#2a3f5f] !p-0 overflow-hidden">
    <div class="overflow-x-auto">
        @forelse ($downloadItems as $downloadItem)
            @php
                $status = $downloadItem->isExpired() ? 'expired' : ($downloadItem->is_active ? 'active' : 'inactive');
            @endphp
            <div class="download-item flex items-center justify-between gap-4 px-6 py-4 border-b border-[#2a3f5f] last:border-b-0 hover:bg-white/5 transition-colors min-w-[900px]"
                 data-filename="{{ strtolower($downloadItem->file_name) }}"
                 data-product="{{ strtolower($downloadItem->product->name) }}"
                 data-product-id="{{ $downloadItem->product_id }}"
                 data-status="{{ $status }}">
                
                <div class="flex-1 min-w-0" style="max-width: 300px;">
                    <h3 class="text-base font-bold text-white truncate mb-2">{{ $downloadItem->file_name }}</h3>
                    <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold truncate">{{ $downloadItem->product->name }}</p>
                </div>

                <div class="flex items-center gap-6 text-sm flex-shrink-0">
                    <div class="flex items-center gap-3">
                        @if ($status === 'expired')
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-red-500/20 text-red-400 border border-red-500/30">Expired</span>
                        @elseif ($status === 'active')
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-green-500/20 text-green-400 border border-green-500/30">Active</span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-gray-500/20 text-gray-400 border border-gray-500/30">Inactive</span>
                        @endif
                        
                        <div class="text-right">
                            <p class="text-xs text-gray-400 mb-1 whitespace-nowrap">Version</p>
                            <p class="text-white font-medium whitespace-nowrap">{{ $downloadItem->version ?? 'N/A' }}</p>
                        </div>
                    </div>
                    
                    <div class="text-right">
                        <p class="text-xs text-gray-400 mb-1 whitespace-nowrap">Size</p>
                        <p class="text-white font-medium whitespace-nowrap">{{ number_format($downloadItem->file_size / 1024 / 1024, 2) }} MB</p>
                    </div>
                    
                    <div class="text-right">
                        <p class="text-xs text-gray-400 mb-1 whitespace-nowrap">Expires</p>
                        <p class="text-white font-medium whitespace-nowrap">{{ $downloadItem->expired_date?->format('M j, Y') ?? 'Never' }}</p>
                    </div>

                    <div class="flex gap-2">
                        <a href="{{ route('admin.download-items.show', $downloadItem) }}" 
                           class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-gray-300 hover:text-white transition-colors whitespace-nowrap">
                            Show    
                        </a>
                        <a href="{{ route('admin.download-items.edit', $downloadItem) }}" 
                           class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-gray-300 hover:text-white transition-colors whitespace-nowrap">
                            Edit
                        </a>
                        <a href="{{ route('admin.download-items.destroy', $downloadItem) }}" 
                           class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-vd-error hover:text-vd-error transition-colors whitespace-nowrap">
                            delete
                        </a>
                         <form id="delete-{{ $downloadItem->id }}"
                            method="POST"
                            action="{{ route('admin.download-items.destroy', $downloadItem) }}"
                            class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="!p-12 text-center">
                <svg class="mx-auto mb-4 h-12 w-12 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                </svg>
                <p class="text-gray-400">No downloads available</p>
            </div>
        @endforelse
    </div>
</div>

{{-- No Results Message --}}
<div id="noDownloadResults" class="hidden vd-card border-[#2a3f5f] !p-12 text-center">
    <svg class="mx-auto mb-4 h-12 w-12 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
    </svg>
    <p class="text-gray-400">No downloads found matching your search criteria.</p>
</div>

{{-- ── Pagination ── --}}
@if ($downloadItems->hasPages())
    <div class="mt-8">
        {{ $downloadItems->links() }}
    </div>
@endif

@endsection

@vite('resources/js/admin-download-filter.js')