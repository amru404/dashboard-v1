@extends('layouts.user')

@section('title', 'My Products')

@section('content')

{{-- ── Page Header ── --}}
<div class="mb-8">
    <h1 class="text-3xl font-bold text-white mb-2">Your Products</h1>
    <p class="text-base text-gray-300">
        Products currently assigned to your account via active entitlements.
    </p>
</div>

{{-- ── Product Cards Grid ── --}}
<div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
    @forelse ($entitlements as $entitlement)
        <div class="vd-card  border-[#2a3f5f] !p-6 flex flex-col">
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <h3 class="text-xl font-bold text-white mb-1">{{ $entitlement->product->name }}</h3>
                    <p class="text-xs tracking-[0.14em] text-vd-primary uppercase font-semibold">{{ $entitlement->product->code }}</p>
                </div>
                <x-badge :active="$entitlement->product->is_active">
                    {{ $entitlement->product->is_active ? 'Active' : 'Inactive' }}
                </x-badge>
            </div>
            
            <p class="text-sm text-gray-400 mb-5">
                {{ $entitlement->product->getCatalogPath() }}
            </p>

            <dl class="flex-1 space-y-3 text-sm">
                <div class="flex justify-between gap-4">
                    <dt class="text-gray-400">Access ends</dt>
                    <dd class="text-white font-medium">{{ $entitlement->end_date?->format('M j, Y') ?? 'Open ended' }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-gray-400">Downloads end</dt>
                    <dd class="text-white font-medium">{{ $entitlement->download_expired_date?->format('M j, Y') ?? 'No limit' }}</dd>
                </div>
            </dl>

            <div class="mt-6 pt-4 border-t border-[#2a3f5f]">
                <a href="{{ route('user.products.show', $entitlement->product) }}" class="inline-flex items-center justify-center w-full px-4 py-2.5 rounded-lg bg-white/10 hover:bg-white/15 text-white font-semibold text-sm border border-white/20 transition-colors">
                    View Product Details →
                </a>
            </div>
        </div>
    @empty
        <div class="vd-card  border-[#2a3f5f] md:col-span-2 xl:col-span-3 !p-12 text-center">
            <svg class="mx-auto mb-4 h-12 w-12 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            <p class="text-gray-400">No products are currently assigned to your account.</p>
        </div>
    @endforelse
</div>

{{-- ── Pagination ── --}}
@if ($entitlements->hasPages())
    <div class="mt-8">
        {{ $entitlements->links() }}
    </div>
@endif

@endsection
