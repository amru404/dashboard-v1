@extends('layouts.user')

@section('title', $product->name)

@section('content')

{{-- ── Page Header ── --}}
<div class="mb-6">
    <div class="vd-card border-[#2a3f5f] !p-6">
        <div class="flex items-start justify-between gap-4 mb-4">
            <div class="flex-1">
                <h1 class="text-3xl font-bold text-white mb-2">{{ $product->name }}</h1>
                <p class="font-mono text-vd-primary text-sm font-semibold">{{ $product->code }}</p>
            </div>
            <div class="flex items-center gap-3">
                <x-badge :active="$entitlement->product->is_active">
                    {{ $entitlement->product->is_active ? 'Active' : 'Inactive' }}
                </x-badge>
                <a href="{{ route('user.products.index') }}" class="inline-flex items-center px-4 py-2.5 rounded-lg bg-white/10 hover:bg-white/15 text-white font-semibold text-sm border border-white/20 transition-colors">
                    ← Back
                </a>
            </div>
        </div>

        {{-- Quick Stats Pills --}}
        <div class="flex flex-wrap gap-3 mb-4">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-vd-primary/10 border border-vd-primary/20">
                <span class="text-xs text-gray-400">Licenses:</span>
                <span class="text-sm font-bold text-vd-primary">{{ $activeLicenseCount }}</span>
            </div>
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-blue-500/10 border border-blue-500/20">
                <span class="text-xs text-gray-400">Downloads:</span>
                <span class="text-sm font-bold text-blue-400">{{ $totalDownloadsCount }}</span>
            </div>
            @if ($daysRemaining !== null)
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-orange-500/10 border border-orange-500/20">
                <span class="text-xs text-gray-400">Days Left:</span>
                <span class="text-sm font-bold text-orange-400">{{ (int)$daysRemaining }}</span>
            </div>
            @endif
        </div>

        {{-- Product Details Grid --}}
        <dl class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4 text-sm">
            <div>
                <dt class="text-xs text-gray-400 mb-1">Path</dt>
                <dd class="text-white font-medium">{{ $product->getCatalogPath() }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400 mb-1">Parent</dt>
                <dd class="text-white font-medium">{{ $product->parent?->name ?? $product->name }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400 mb-1">Access Ends</dt>
                <dd class="text-white font-medium">{{ $entitlement->end_date?->format('M j, Y') ?? 'Open ended' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400 mb-1">Downloads End</dt>
                <dd class="text-white font-medium">{{ $entitlement->download_expired_date?->format('M j, Y') ?? 'No limit' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400 mb-1">Organization</dt>
                <dd class="text-white font-medium">{{ auth()->user()->organization?->name ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400 mb-1">Entitlement</dt>
                <dd class="text-white font-medium capitalize">{{ $entitlement->status }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400 mb-1">License Model</dt>
                <dd class="text-white font-medium">{{ $licenses->first()?->licenseType?->name ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400 mb-1">Last Access</dt>
                <dd class="text-white font-medium">{{ $lastAccessedDate?->format('M j, Y') ?? 'Never' }}</dd>
            </div>
        </dl>

        @if ($product->description)
            <div class="mt-4 pt-4 border-t border-[#2a3f5f]">
                <p class="text-sm text-gray-300 leading-relaxed">{{ $product->description }}</p>
            </div>
        @endif
    </div>
</div>

{{-- ── Two Column Layout ── --}}
<div class="grid gap-6 lg:grid-cols-3">

    {{-- Left: Sub Products & License Keys ── --}}
    <div class="lg:col-span-2 space-y-6">
        
        @if ($product->subProducts->isNotEmpty())
            {{-- Sub Products Section --}}
            <div class="vd-card border-[#2a3f5f] !p-6">
                <div class="flex items-start justify-between gap-4 mb-5">
                    <h2 class="text-xl font-bold text-white">Sub Products</h2>
                    <div class="text-sm text-gray-400">
                        <span id="subProductCount">{{ $product->subProducts->count() }}</span> 
                        <span id="subProductLabel">{{ $product->subProducts->count() === 1 ? 'item' : 'items' }}</span>
                    </div>
                </div>

                {{-- Search & Filter Bar --}}
                <div class="grid gap-3 sm:grid-cols-[1fr_auto] mb-4">
                    <div class="relative">
                        <input 
                            type="text" 
                            id="subProductSearch"
                            placeholder="Search sub-products by name or code..."
                            class="w-full px-4 py-2.5 pl-10 rounded-lg bg-[#0f1829] border border-[#2a3f5f] text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-vd-primary/50 focus:border-vd-primary transition-colors text-sm" />
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    
                    <select 
                        id="subProductStatus" 
                        class="px-4 py-2.5 rounded-lg bg-[#0f1829] border border-[#2a3f5f] text-white focus:outline-none focus:ring-2 focus:ring-vd-primary/50 focus:border-vd-primary transition-colors text-sm min-w-[140px]">
                        <option value="all">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                
                <div id="subProductsContainer" class="space-y-4">
                    @foreach ($product->subProducts as $subProduct)
                        <div class="sub-product-item rounded-lg border border-[#2a3f5f] bg-[#0f1829]/40 p-4" 
                             data-name="{{ strtolower($subProduct->name) }}" 
                             data-code="{{ strtolower($subProduct->code) }}"
                             data-status="{{ $subProduct->is_active ? 'active' : 'inactive' }}">
                            <div class="flex items-start justify-between gap-3 mb-3">
                                <div class="flex-1">
                                    <h3 class="text-base font-bold text-white mb-1">{{ $subProduct->name }}</h3>
                                    <p class="text-xs text-gray-400 font-mono">{{ $subProduct->code }}</p>
                                </div>
                                <div class="flex items-center gap-2">
                                     <x-badge :active="$subProduct->is_active">
                                        {{ $subProduct->is_active ? 'Active' : 'Inactive' }}
                                    </x-badge>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- No Results Message --}}
                <div id="noSubProductResults" class="hidden text-center py-8">
                    <svg class="mx-auto mb-3 h-10 w-10 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <p class="text-sm text-gray-400">No sub-products found</p>
                </div>
            </div>
        @endif
        </div>

    {{-- Right: Downloads ── --}}
    <div class="lg:col-span-1">
        <div class="vd-card border-[#2a3f5f] !p-6">
            <h2 class="text-lg font-bold text-white mb-5">Available Downloads</h2>
            <div class="space-y-3">
                @forelse ($downloads as $download)
                    <div class="rounded-lg border border-[#2a3f5f] bg-[#0f1829]/40 p-4 hover:bg-[#0f1829]/60 transition-colors">
                        <div class="mb-3">
                            <h3 class="text-sm font-semibold text-white mb-1">{{ $download->file_name }}</h3>
                            <div class="flex items-center gap-2 text-xs text-gray-400">
                                <span>v{{ $download->version ?? 'N/A' }}</span>
                                <span>•</span>
                                <span>{{ number_format($download->file_size / 1024 / 1024, 2) }} MB</span>
                            </div>
                        </div>
                        <a 
                            href="{{ route('user.downloads.download', $download) }}" 
                            class="inline-flex items-center justify-center w-full px-4 py-2 rounded-lg bg-vd-primary/20 hover:bg-vd-primary/30 text-vd-primary font-semibold text-sm border border-vd-primary/30 transition-colors"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                            </svg>
                            Download
                        </a>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <svg class="mx-auto mb-3 h-10 w-10 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                        </svg>
                        <p class="text-sm text-gray-400">No downloads available</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection

@vite('resources/js/sub-product-filter.js')