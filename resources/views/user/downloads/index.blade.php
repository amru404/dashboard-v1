@extends('layouts.user')

@section('title', 'My Downloads')

@section('content')

@php
    $layout = request('layout', 'grid');
    $isGrid = $layout === 'grid';
@endphp

{{-- ── Page Header ── --}}
<div class="mb-8">
    <div class="flex items-start justify-between gap-4 mb-6">
        <div class="flex-1">
            <h1 class="text-3xl font-bold text-white mb-2">Downloads</h1>
            <p class="text-sm text-gray-400">Files available through your active product entitlements</p>
        </div>

        {{-- Layout Switcher --}}
        <div class="flex items-center gap-2 rounded-lg border border-[#2a3f5f] bg-[#0f1829] p-1">
            <a href="{{ route('user.downloads.index', array_merge(request()->except('page'), ['layout' => 'grid'])) }}"
               class="inline-flex items-center justify-center w-9 h-9 rounded-lg transition-colors {{ $isGrid ? 'bg-vd-primary text-white' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}"
               title="Grid view">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4h6v6H4V4zm10 0h6v6h-6V4zM4 14h6v6H4v-6zm10 0h6v6h-6v-6z" />
                </svg>
            </a>
            <a href="{{ route('user.downloads.index', array_merge(request()->except('page'), ['layout' => 'list'])) }}"
               class="inline-flex items-center justify-center w-9 h-9 rounded-lg transition-colors {{ !$isGrid ? 'bg-vd-primary text-white' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}"
               title="List view">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 5h16M4 12h16M4 19h16" />
                </svg>
            </a>
        </div>
    </div>

    {{-- Search & Filter --}}
    <form method="GET" action="{{ route('user.downloads.index') }}" id="filterForm" class="grid gap-3 sm:grid-cols-[1fr_auto]">
        <input type="hidden" name="layout" value="{{ $layout }}" />
        
        <div class="relative">
            <input type="text" 
                   id="search" 
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Search files..."
                   class="w-full px-4 py-2.5 rounded-lg bg-[#0f1829] border border-[#2a3f5f] text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-vd-primary/50 focus:border-vd-primary transition-colors text-sm" />
            <div id="searchSpinner" class="hidden absolute right-3 top-1/2 -translate-y-1/2">
                <svg class="animate-spin h-4 w-4 text-vd-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        </div>
        
        <select id="product_id" 
                name="product_id" 
                class="px-4 py-2.5 rounded-lg bg-[#0f1829] border border-[#2a3f5f] text-white focus:outline-none focus:ring-2 focus:ring-vd-primary/50 focus:border-vd-primary transition-colors text-sm">
            <option value="">All products</option>
            @foreach ($filterProducts ?? [] as $fp)
                <option value="{{ $fp->id }}" @selected(request('product_id') == $fp->id)>
                    {{ $fp->name }}
                </option>
            @endforeach
        </select>
    </form>
</div>

{{-- ── Stats Cards ── --}}
<div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
    <div class="vd-card border-[#2a3f5f] !p-5">
        <p class="text-xs text-gray-400 uppercase tracking-wider mb-2">Total files</p>
        <p class="text-3xl font-bold text-white">{{ $downloadItems->total() }}</p>
    </div>
    
    <div class="vd-card border-[#2a3f5f] !p-5">
        <p class="text-xs text-gray-400 uppercase tracking-wider mb-2">Active</p>
        <p class="text-3xl font-bold text-vd-primary">{{ $downloadItems->where('is_active', true)->count() }}</p>
    </div>
    
    <div class="vd-card border-[#2a3f5f] !p-5">
        <p class="text-xs text-gray-400 uppercase tracking-wider mb-2">Total size</p>
        <p class="text-3xl font-bold text-white">
            {{ number_format($downloadItems->sum('file_size') / 1024 / 1024, 0) }} MB
        </p>
    </div>
    
    <div class="vd-card border-[#2a3f5f] !p-5">
        <p class="text-xs text-gray-400 uppercase tracking-wider mb-2">Products</p>
        <p class="text-3xl font-bold text-white">{{ $downloadItems->pluck('product_id')->unique()->count() }}</p>
    </div>
</div>

{{-- ── Download Cards ── --}}
@if($isGrid)
    {{-- Grid Layout --}}
    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
        @forelse ($downloadItems as $downloadItem)
            <x-download-card
                :download-item="$downloadItem"
                :download-url="route('user.downloads.download', $downloadItem)"
                layout="grid" />
        @empty
            <div class="vd-card border-[#2a3f5f] md:col-span-2 xl:col-span-3 !p-12 text-center">
                <svg class="mx-auto mb-4 h-12 w-12 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                </svg>
                <p class="text-gray-400">No downloads available</p>
            </div>
        @endforelse
    </div>
@else
    {{-- List Layout --}}
    <div class="vd-card border-[#2a3f5f] !p-0 overflow-hidden">
        <div class="overflow-x-auto">
            @forelse ($downloadItems as $downloadItem)
                <div class="flex items-center justify-between gap-4 px-6 py-4 border-b border-[#2a3f5f] last:border-b-0 hover:bg-white/5 transition-colors min-w-[800px]">
                    <div class="flex-1 min-w-0" style="max-width: 300px;">
                        <h3 class="text-base font-bold text-white truncate mb-2">{{ $downloadItem->file_name }}</h3>
                        <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold truncate">{{ $downloadItem->product->name }}</p>
                    </div>

                    <div class="flex items-center gap-6 text-sm flex-shrink-0">
                        <div class="flex items-center gap-3">
                            <x-badge :active="$downloadItem->is_active">
                                {{ $downloadItem->is_active ? 'Active' : 'Inactive' }}
                            </x-badge>
                            
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

                        <a href="{{ route('user.downloads.download', $downloadItem) }}" 
                           class="inline-flex items-center justify-center px-6 py-2 rounded-lg bg-vd-primary hover:bg-vd-primary/90 text-white font-semibold text-sm transition-colors whitespace-nowrap">
                            Download
                        </a>
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
@endif

{{-- ── Pagination ── --}}
@if ($downloadItems->hasPages())
    <div class="mt-8">
        {{ $downloadItems->appends(['layout' => $layout, 'search' => request('search'), 'product_id' => request('product_id')])->links() }}
    </div>
@endif

{{-- Auto-submit Search & Filter Script --}}
<script>
(function() {
    const form = document.getElementById('filterForm');
    const searchInput = document.getElementById('search');
    const productSelect = document.getElementById('product_id');
    const spinner = document.getElementById('searchSpinner');
    
    let searchTimeout = null;
    
    function showSpinner() {
        if (spinner) spinner.classList.remove('hidden');
    }
    
    function hideSpinner() {
        if (spinner) spinner.classList.add('hidden');
    }
    
    // Debounced search - submit after user stops typing (500ms delay)
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        showSpinner();
        
        searchTimeout = setTimeout(function() {
            form.submit();
        }, 500);
    });
    
    // Instant filter on select change
    productSelect.addEventListener('change', function() {
        showSpinner();
        form.submit();
    });
    
    // Submit on Enter key
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            clearTimeout(searchTimeout);
            showSpinner();
            form.submit();
        }
    });
    
    // Hide spinner when page loads (in case of back button)
    window.addEventListener('pageshow', function() {
        hideSpinner();
    });
})();
</script>

@endsection
