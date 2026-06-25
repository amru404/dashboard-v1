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
                @if ($product->is_active)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-500/20 text-green-400 border border-green-500/30">
                        ✓ Active
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-500/20 text-gray-400 border border-gray-500/30">
                        Inactive
                    </span>
                @endif
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
                <span class="text-sm font-bold text-orange-400">{{ $daysRemaining }}</span>
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
                <dd class="text-white font-medium">{{ $product->parent?->name ?? 'Root' }}</dd>
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

    {{-- Left: License Keys ── --}}
    <div class="lg:col-span-2 space-y-6">
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
                            <div class="flex items-center gap-3 text-xs text-gray-400">
                                <span>Expires: {{ $license->expired_date?->format('M j, Y') ?? 'Never' }}</span>
                                <span>•</span>
                                <span>{{ $license->activeActivationCount() }} / {{ $license->max_activations ?? '∞' }} activations</span>
                            </div>
                        </div>
                        @if ($license->isExpired())
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-red-500/20 text-red-400 border border-red-500/30">
                                Expired
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-green-500/20 text-green-400 border border-green-500/30">
                                Active
                            </span>
                        @endif
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <div class="flex-1 font-mono text-sm bg-black/30 rounded px-3 py-2 border border-[#2a3f5f]">
                            <span x-show="!show" class="text-gray-400">••••-••••-••••-••••</span>
                            <span x-show="show" class="text-white">{{ $license->license_key }}</span>
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
                <div class="text-center py-8 text-sm text-gray-400">
                    No license keys available for this product.
                </div>
            @endforelse
        </div>
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
