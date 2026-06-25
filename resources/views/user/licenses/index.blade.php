@extends('layouts.user')

@section('title', 'My Licenses')

@section('content')

@php use Illuminate\Support\Str; @endphp

{{-- ── Page Header ── --}}
<div class="mb-8">
    <h1 class="text-3xl font-bold text-white mb-2">License Keys</h1>
    <p class="text-base text-gray-300">
        Browse your software products and license keys in a clean enterprise-style tree.
    </p>
</div>

<div class="space-y-6">
    @forelse ($rootProducts as $product)
        <section x-data="{ open: true }" class="vd-card border-[#2a3f5f] !p-0 overflow-hidden">
            <button type="button" @click="open = !open" class="w-full flex items-center justify-between gap-4 px-6 py-5 bg-[#071422] hover:bg-[#0a1d31] transition-colors">
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-start text-vd-primary uppercase tracking-[0.18em] mb-2"> product</p>
                    <h2 class="text-xl font-bold text-white truncate">{{ $product->name }}</h2>
                    <p class="mt-1 text-start text-sm text-vd-muted truncate">{{ $product->code }} <span> Total Module : {{ $product->subProducts->count() }} </span></p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center rounded-full border border-vd-border bg-vd-secondary/80 px-3 py-1 text-xs font-semibold text-vd-on-surface">
                        {{ $product->licenses->count() }} {{ Str::plural('license', $product->licenses->count()) }}
                    </span>
                    <span class="inline-flex items-center rounded-full border border-vd-border bg-[#102836] px-3 py-1 text-xs font-semibold text-vd-on-surface">
                        {{ $product->totalActiveActivations() }} / {{ $product->totalMaxActivations() ?: 0 }} activations
                    </span>
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-vd-border bg-vd-secondary text-vd-primary">
                        <svg x-show="open" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </span>
                </div>
            </button>

            <div x-show="open" x-collapse class="space-y-5 border-t border-vd-border bg-[#091729] p-6">
                @if ($product->licenses->isNotEmpty())
                    <div class="space-y-4">
                        <div class="rounded-3xl border border-vd-border bg-[#0f1829]/80 p-5 shadow-sm">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-white">{{ $product->name }} licenses</p>
                                    <p class="text-xs text-vd-muted">{{ $product->code }}</p>
                                </div>
                                <div class="flex flex-wrap items-center gap-2 text-xs text-vd-muted">
                                    <span class="inline-flex items-center rounded-full border border-vd-border bg-vd-secondary/80 px-3 py-1 text-xs font-semibold text-vd-on-surface">
                                        {{ $product->licenses->count() }} {{ Str::plural('key', $product->licenses->count()) }}
                                    </span>
                                    <span class="inline-flex items-center rounded-full border border-vd-border bg-[#102836] px-3 py-1 text-xs font-semibold text-vd-primary">
                                        {{ $product->licenses->sum(fn($license) => $license->activeActivationCount()) }} active
                                    </span>
                                    <span class="inline-flex items-center rounded-full border border-vd-border bg-[#102836] px-3 py-1 text-xs font-semibold text-vd-primary">
                                        {{ $product->licenses->sum(fn($license) => $license->max_activations ?? 0) }} max
                                    </span>
                                </div>
                            </div>

                            <div class="mt-4 space-y-3 divide-y divide-vd-border">
                                @foreach ($product->licenses as $license)
                                    <div class="grid gap-4 py-4 lg:grid-cols-[1.6fr_1.1fr_0.9fr] items-center">
                                        <div class="rounded-2xl border border-vd-border bg-black/10 p-4 font-mono text-sm break-all">
                                            <p class="text-xs uppercase tracking-[0.2em] text-vd-muted mb-2">Key</p>
                                            <p class="text-white">{{ $license->masked_license_key }}</p>
                                            <p class="mt-2 text-xs text-vd-muted">Type: {{ $license->licenseType->name }}</p>
                                        </div>

                                        <div class="space-y-1">
                                            <p class="text-xs uppercase tracking-[0.2em] text-vd-muted">Status</p>
                                            <p class="font-semibold {{ $license->isExpired() ? 'text-red-400' : 'text-green-300' }}">
                                                {{ $license->isExpired() ? 'Expired' : 'Active' }}
                                            </p>
                                            <p class="text-xs text-vd-muted">
                                                Expires: {{ $license->expired_date?->format('M j, Y') ?? 'Never' }}
                                            </p>
                                        </div>

                                        <div class="space-y-2 text-right sm:text-left">
                                            <div class="text-xs uppercase tracking-[0.2em] text-vd-muted">Activations</div>
                                            <div class="inline-flex items-center gap-2 rounded-full border border-vd-border bg-vd-secondary/80 px-3 py-1 text-xs font-semibold text-vd-on-surface">
                                                {{ $license->activeActivationCount() }} / {{ $license->max_activations ?? '∞' }}
                                            </div>
                                            <a href="{{ route('user.licenses.show', $license) }}" class="inline-flex items-center rounded-full border border-vd-border bg-white/5 px-3 py-1 text-xs font-semibold text-vd-primary hover:bg-white/10">
                                                Details
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                @if ($product->allChildren->isNotEmpty())
                    <div class="space-y-4">
                        @foreach ($product->allChildren as $childProduct)
                            <div class="pl-4 border-l border-vd-border">
                                @include('user.licenses._product-tree-node', ['product' => $childProduct])
                            </div>
                        @endforeach
                    </div>
                @endif

                @if ($product->licenses->isEmpty() && $product->allChildren->isEmpty())
                    <div class="rounded-2xl border border-vd-border bg-[#0c1a2e] p-5 text-sm text-vd-muted">
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
            <p class="text-gray-400 mb-4">No license keys are assigned to your account.</p>
        </div>
    @endforelse
</div>

@if ($rootProducts->hasPages())
    <div class="mt-8">
        {{ $rootProducts->links() }}
    </div>
@endif

@endsection
