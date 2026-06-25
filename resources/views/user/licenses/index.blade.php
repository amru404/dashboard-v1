@extends('layouts.user')

@section('title', 'My Licenses')

@section('content')

@php use Illuminate\Support\Str; @endphp

{{-- ── Page Header ── --}}
<div class="mb-8">
    <div class="flex items-start justify-between gap-4 mb-6">
        <div class="flex-1">
            <h1 class="text-3xl font-bold text-white mb-2">License Keys</h1>
            <p class="text-base text-gray-300">
                Browse your software products and license keys.
            </p>
        </div>
        <div class="text-sm text-gray-400">
            <span id="licenseCount">{{ $rootProducts->sum(fn($p) => $p->licenses->count() + $p->allChildren->sum(fn($c) => $c->licenses->count())) }}</span>
            <span id="licenseLabel">{{ $rootProducts->sum(fn($p) => $p->licenses->count() + $p->allChildren->sum(fn($c) => $c->licenses->count())) === 1 ? 'license' : 'licenses' }}</span>
        </div>
    </div>

    {{-- Search & Filter Bar --}}
    <x-card>    
    <div class="grid gap-3 sm:grid-cols-[1fr_auto]">
        <div class="relative">
            <input 
                type="text" 
                id="licenseSearch"
                placeholder="Search by product name, code, or license key..."
                class="w-full px-4 py-2.5 pl-10 rounded-lg bg-[#0f1829] border border-[#2a3f5f] text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-vd-primary/50 focus:border-vd-primary transition-colors text-sm" />
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>
        
        <select 
            id="licenseStatus" 
            class="px-4 py-2.5 rounded-lg bg-[#0f1829] border border-[#2a3f5f] text-white focus:outline-none focus:ring-2 focus:ring-vd-primary/50 focus:border-vd-primary transition-colors text-sm min-w-[140px]">
            <option value="all">All Status</option>
            <option value="active">Active</option>
            <option value="expired">Expired</option>
        </select>
    </div>
</div>
    </x-card>   

<div class="space-y-6">
    @forelse ($rootProducts as $product)
        <section x-data="{ open: true }" 
                 class="product-section vd-card border-[#2a3f5f] !p-0 overflow-hidden"
                 data-product-name="{{ strtolower($product->name) }}"
                 data-product-code="{{ strtolower($product->code) }}">
            {{-- Product Header --}}
            <button type="button" @click="open = !open" class="w-full flex items-center justify-between gap-4 px-6 py-4 bg-[#071422] hover:bg-[#0a1d31] transition-colors">
                <div class="flex-1 min-w-0 text-left">
                    <h2 class="text-lg font-bold text-white mb-1">{{ $product->name }}</h2>
                    <div class="flex items-center gap-3 text-xs text-gray-400">
                        <span class="font-mono">{{ $product->code }}</span>
                        @if ($product->subProducts->count() > 0)
                            <span>•</span>
                            <span>{{ $product->subProducts->count() }} {{ Str::plural('module', $product->subProducts->count()) }}</span>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center rounded-full border border-vd-border bg-vd-primary/10 px-3 py-1 text-xs font-semibold text-vd-primary">
                        {{ $product->licenses->count() }} {{ Str::plural('key', $product->licenses->count()) }}
                    </span>
                    <span class="inline-flex items-center rounded-full border border-vd-border bg-blue-500/10 px-3 py-1 text-xs font-semibold text-blue-400">
                        {{ $product->totalActiveActivations() }} / {{ $product->totalMaxActivations() ?: '∞' }}
                    </span>
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-vd-border bg-vd-secondary text-vd-primary">
                        <svg x-show="open" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </span>
                </div>
            </button>

            {{-- Product Content --}}
            <div x-show="open" x-collapse class="border-t border-vd-border bg-[#091729] p-6">
                @if ($product->licenses->isNotEmpty())
                    <div class="space-y-3">
                        @foreach ($product->licenses as $license)
                            <div 
                                class="license-item rounded-lg border border-[#2a3f5f] bg-[#0f1829]/40 p-4"
                                x-data="{ show: false }"
                                data-license-key="{{ strtolower($license->license_key) }}"
                                data-license-status="{{ $license->isExpired() ? 'expired' : 'active' }}"
                                data-license-product="{{ strtolower($product->name) }}"
                                data-license-subproduct="{{ $license->subProduct ? strtolower($license->subProduct->name) : '' }}"
                            >
                                <div class="grid gap-4 lg:grid-cols-[2fr_1fr_1fr_auto] items-center">
                                    {{-- License Key --}}
                                    <div>
                                        <p class="text-xs text-gray-400 mb-2">License Key</p>
                                        <div class="flex items-center gap-2">
                                            <div class="flex-1 font-mono text-sm bg-black/30 rounded px-3 py-2 border border-[#2a3f5f]">
                                                <span x-show="!show" class="text-gray-400">••••-••••-••••-••••</span>
                                                <span x-show="show" class="text-white break-all">{{ $license->license_key }}</span>
                                            </div>
                                            <button 
                                                @click="show = !show"
                                                class="flex-shrink-0 inline-flex items-center justify-center w-9 h-9 rounded-lg bg-white/10 hover:bg-white/15 border border-white/20 transition-colors text-gray-300 hover:text-white"
                                                :title="show ? 'Hide' : 'Show'"
                                            >
                                                <svg x-show="!show" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                <svg x-show="show" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="display: none;">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                                </svg>
                                            </button>
                                        </div>
                                        <p class="text-xs text-gray-400 mt-2">Type: {{ $license->licenseType->name }}</p>
                                    </div>

                                    {{-- Status --}}
                                    <div>
                                        <p class="text-xs text-gray-400 mb-2">Status</p>
                                        @if ($license->isExpired())
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-500/20 text-red-400 border border-red-500/30">
                                                Expired
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-500/20 text-green-400 border border-green-500/30">
                                                Active
                                            </span>
                                        @endif
                                        <p class="text-xs text-gray-400 mt-2">
                                            Expires: {{ $license->expired_date?->format('M j, Y') ?? 'Never' }}
                                        </p>
                                    </div>

                                    {{-- Activations --}}
                                    <div>
                                        <p class="text-xs text-gray-400 mb-2">Activations</p>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-500/20 text-blue-400 border border-blue-500/30">
                                            {{ $license->activeActivationCount() }} / {{ $license->max_activations ?? '∞' }}
                                        </span>
                                    </div>

                                    {{-- Action --}}
                                    <div>
                                        <a href="{{ route('user.licenses.show', $license) }}" class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-vd-primary/20 hover:bg-vd-primary/30 text-vd-primary font-semibold text-xs border border-vd-primary/30 transition-colors">
                                            Details →
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if ($product->allChildren->isNotEmpty())
                    <div class="mt-6 space-y-4">
                        <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Sub-products</div>
                        @foreach ($product->allChildren as $childProduct)
                            <div class="pl-4 border-l-2 border-[#2a3f5f]">
                                @include('user.licenses._product-tree-node', ['product' => $childProduct])
                            </div>
                        @endforeach
                    </div>
                @endif

                @if ($product->licenses->isEmpty() && $product->allChildren->isEmpty())
                    <div class="text-center py-8 text-sm text-gray-400">
                        No license keys found for this product.
                    </div>
                @endif
            </div>
        </section>
    @empty
        <div class="vd-card border-[#2a3f5f] !p-12 text-center">
            <svg class="mx-auto mb-4 h-12 w-12 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
            </svg>
            <p class="text-gray-400">No license keys are assigned to your account.</p>
        </div>
    @endforelse

    {{-- No Results Message (for filtering) --}}
    <div id="noLicenseResults" class="hidden vd-card border-[#2a3f5f] !p-12 text-center">
        <svg class="mx-auto mb-4 h-12 w-12 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <p class="text-gray-400">No licenses found matching your search criteria.</p>
    </div>
</div>

@if ($rootProducts->hasPages())
    <div class="mt-8">
        {{ $rootProducts->links() }}
    </div>
@endif

@endsection

    @vite('resources/js/license-filter.js')
