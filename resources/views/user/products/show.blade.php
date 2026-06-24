@extends('layouts.user')

@section('title', $product->name)

@section('content')

{{-- ── Page Header ── --}}
<div class="mb-6">
    <div class="flex items-start justify-between gap-4 mb-2">
        <div class="flex-1">
            <h1 class="text-3xl font-bold text-white mb-2">{{ $product->name }}</h1>
            <p class="text-sm text-gray-400">{{ $product->getCatalogPath() }}</p>
        </div>
        <a href="{{ route('user.products.index') }}" class="inline-flex items-center px-4 py-2.5 rounded-lg bg-white/10 hover:bg-white/15 text-white font-semibold text-sm border border-white/20 transition-colors">
            ← Back to Products
        </a>
    </div>
</div>

{{-- ── Three Section Layout ── --}}
<div class="grid gap-6 lg:grid-cols-3">

    {{-- Section 1: Product Information ── --}}
    <div class="lg:col-span-2 space-y-6">
        
        {{-- Product Info Card --}}
        <div class="vd-card border-[#2a3f5f] !p-6">
            <div class="flex items-start justify-between mb-5">
                <div>
                    <h2 class="text-xl font-bold text-white mb-1">Product Information</h2>
                    <p class="text-xs tracking-[0.14em] text-vd-primary uppercase font-semibold">{{ $product->code }}</p>
                </div>
                @if ($product->is_active)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-500/20 text-green-400 border border-green-500/30">
                        ✓ Active
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-500/20 text-gray-400 border border-gray-500/30">
                        Inactive
                    </span>
                @endif
            </div>

            <dl class="grid gap-4 sm:grid-cols-2 mb-6">
                <div class="rounded-lg border border-[#2a3f5f] bg-[#0f1829]/40 p-4">
                    <dt class="text-xs text-gray-400 uppercase tracking-widest mb-2 font-semibold">Product Code</dt>
                    <dd class="text-base font-semibold text-white">{{ $product->code }}</dd>
                </div>
                <div class="rounded-lg border border-[#2a3f5f] bg-[#0f1829]/40 p-4">
                    <dt class="text-xs text-gray-400 uppercase tracking-widest mb-2 font-semibold">Breadcrumb Path</dt>
                    <dd class="text-base font-semibold text-white">{{ $product->getCatalogPath() }}</dd>
                </div>
                <div class="rounded-lg border border-[#2a3f5f] bg-[#0f1829]/40 p-4">
                    <dt class="text-xs text-gray-400 uppercase tracking-widest mb-2 font-semibold">Parent Product</dt>
                    <dd class="text-base font-semibold text-white">{{ $product->parent?->name ?? 'Root Product' }}</dd>
                </div>
                <div class="rounded-lg border border-[#2a3f5f] bg-[#0f1829]/40 p-4">
                    <dt class="text-xs text-gray-400 uppercase tracking-widest mb-2 font-semibold">Organization</dt>
                    <dd class="text-base font-semibold text-white">{{ auth()->user()->organization?->name ?? 'N/A' }}</dd>
                </div>
                <div class="rounded-lg border border-[#2a3f5f] bg-[#0f1829]/40 p-4">
                    <dt class="text-xs text-gray-400 uppercase tracking-widest mb-2 font-semibold">Access Ends</dt>
                    <dd class="text-base font-semibold text-white">{{ $entitlement->end_date?->format('M j, Y') ?? 'Open ended' }}</dd>
                </div>
                <div class="rounded-lg border border-[#2a3f5f] bg-[#0f1829]/40 p-4">
                    <dt class="text-xs text-gray-400 uppercase tracking-widest mb-2 font-semibold">Downloads End</dt>
                    <dd class="text-base font-semibold text-white">{{ $entitlement->download_expired_date?->format('M j, Y') ?? 'No limit' }}</dd>
                </div>
                <div class="rounded-lg border border-[#2a3f5f] bg-[#0f1829]/40 p-4">
                    <dt class="text-xs text-gray-400 uppercase tracking-widest mb-2 font-semibold">Entitlement Type</dt>
                    <dd class="text-base font-semibold text-white capitalize">{{ $entitlement->status }}</dd>
                </div>
                <div class="rounded-lg border border-[#2a3f5f] bg-[#0f1829]/40 p-4">
                    <dt class="text-xs text-gray-400 uppercase tracking-widest mb-2 font-semibold">License Model</dt>
                    <dd class="text-base font-semibold text-white">{{ $licenses->first()?->licenseType?->name ?? 'N/A' }}</dd>
                </div>
            </dl>

            @if ($product->description)
                <div class="border-t border-[#2a3f5f] pt-4">
                    <p class="text-sm text-gray-300 leading-relaxed whitespace-pre-line">
                        {{ $product->description }}
                    </p>
                </div>
            @endif
        </div>

        {{-- License Keys Section --}}
        <div class="vd-card border-[#2a3f5f] !p-6">
            <h2 class="text-xl font-bold text-white mb-5">License Keys</h2>
            
            @forelse ($licenses as $license)
                <div 
                    class="mb-4 last:mb-0 rounded-lg border border-[#2a3f5f] bg-[#0f1829]/40 p-4"
                    x-data="{ show: false }"
                >
                    <div class="flex items-start justify-between gap-4 mb-3">
                        <div class="flex-1">
                            <h3 class="text-sm font-semibold text-white mb-1">{{ $license->licenseType->name }}</h3>
                            <p class="text-xs text-gray-400">
                                Expires: {{ $license->expired_date?->format('M j, Y') ?? 'Never' }}
                            </p>
                        </div>
                        <span class="text-xs text-gray-400">
                            {{ $license->activations->count() }} / {{ $license->max_activations ?? '∞' }} activations
                        </span>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <div class="flex-1 font-mono text-sm bg-black/30 rounded px-3 py-2 border border-[#2a3f5f]">
                            <span x-show="!show" class="text-gray-400">••••-••••-••••-••••</span>
                            <span x-show="show" class="text-white" x-text="'{{ $license->license_key }}'"></span>
                        </div>
                        <button 
                            @click="show = !show"
                            class="flex-shrink-0 inline-flex items-center justify-center w-10 h-10 rounded-lg bg-white/10 hover:bg-white/15 border border-white/20 transition-colors text-gray-300 hover:text-white"
                            :title="show ? 'Hide key' : 'Show key'"
                        >
                            <svg x-show="!show" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="show" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>
                    </div>
                </div>
            @empty
                <div class="text-center py-6 text-sm text-gray-400">
                    No license keys available for this product.
                </div>
            @endforelse
        </div>
    </div>

    {{-- Right Column ── --}}
    <div class="lg:col-span-1 space-y-6">
        
        {{-- Section 2: Entitlement Summary ── --}}
        <div class="vd-card border-[#2a3f5f] !p-6">
            <h2 class="text-lg font-bold text-white mb-5">Entitlement Summary</h2>
            <div class="space-y-4">
                <div class="rounded-lg border border-[#2a3f5f] bg-[#0f1829]/40 p-4">
                    <dt class="text-xs text-gray-400 uppercase tracking-widest mb-2 font-semibold">Active Licenses</dt>
                    <dd class="text-2xl font-bold text-vd-primary">{{ $activeLicenseCount }}</dd>
                </div>
                <div class="rounded-lg border border-[#2a3f5f] bg-[#0f1829]/40 p-4">
                    <dt class="text-xs text-gray-400 uppercase tracking-widest mb-2 font-semibold">Total Downloads</dt>
                    <dd class="text-2xl font-bold text-vd-primary">{{ $totalDownloadsCount }}</dd>
                </div>
                <div class="rounded-lg border border-[#2a3f5f] bg-[#0f1829]/40 p-4">
                    <dt class="text-xs text-gray-400 uppercase tracking-widest mb-2 font-semibold">Days Remaining</dt>
                    <dd class="text-2xl font-bold text-white">
                        @if ($daysRemaining !== null)
                            {{ $daysRemaining }}
                            <span class="text-sm font-normal text-gray-400">days</span>
                        @else
                            <span class="text-base font-normal text-gray-400">Unlimited</span>
                        @endif
                    </dd>
                </div>
                <div class="rounded-lg border border-[#2a3f5f] bg-[#0f1829]/40 p-4">
                    <dt class="text-xs text-gray-400 uppercase tracking-widest mb-2 font-semibold">Last Accessed</dt>
                    <dd class="text-sm font-semibold text-white">
                        {{ $lastAccessedDate?->format('M j, Y') ?? 'Never' }}
                    </dd>
                </div>
            </div>
        </div>

        {{-- Section 3: Available Downloads ── --}}
        <div class="vd-card border-[#2a3f5f] !p-6">
            <h2 class="text-lg font-bold text-white mb-5">Available Downloads</h2>
            <div class="space-y-3">
                @forelse ($downloads as $download)
                    <div class="rounded-lg border border-[#2a3f5f] bg-[#0f1829]/40 p-4 hover:bg-[#0f1829]/60 transition-colors">
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div class="flex-1 min-w-0">
                                <h3 class="text-sm font-semibold text-white truncate">{{ $download->file_name }}</h3>
                                <p class="text-xs text-gray-400 mt-1">Version {{ $download->version ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-xs text-gray-400">
                                {{ number_format($download->file_size / 1024 / 1024, 2) }} MB
                            </span>
                            <a 
                                href="{{ route('user.downloads.download', $download) }}" 
                                class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-vd-primary hover:text-vd-primary/80 transition-colors"
                            >
                                <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                                </svg>
                                Download
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6">
                        <svg class="mx-auto mb-3 h-10 w-10 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                        </svg>
                        <p class="text-sm text-gray-400">No downloads available for this product.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection
