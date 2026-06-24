@extends('layouts.user')

@section('title', $product->name)

@section('content')

<div class="mb-6">
    <div class="flex items-start justify-between gap-4 mb-2  vd-card">
        <div class="flex-1">
            <h1 class="text-3xl font-bold text-white mb-2">{{ $product->name }}</h1>
            <p class="text-base text-gray-300">{{ $product->getCatalogPath() }}</p>
        </div>
        <a href="{{ route('user.products.index') }}" class="inline-flex items-center px-4 py-2.5 rounded-lg bg-white/10 hover:bg-white/15 text-white font-semibold text-sm border border-white/20 transition-colors">
            ← Back to Products
        </a>
    </div>
</div>


{{-- ── Two Column Layout: Details + Downloads ── --}}
<div class="grid gap-6 lg:grid-cols-3 mb-6">

    {{-- Left: Product Details ── --}}
    <div class="lg:col-span-2">
    <div class="vd-card  border-[#2a3f5f] !p-6">
        <div class="flex items-start justify-between mb-5">
            <div>
                <h2 class="text-xl font-bold text-white">Product Details</h2>
                @if ($product->parent)
                    <p class="text-sm text-gray-400 mt-1">
                        Parent: {{ $product->parent->name }}
                        @if ($product->parent->parent)
                            · Grandparent: {{ $product->parent->parent->name }}
                        @endif
                    </p>
                @endif
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
        
        <p class="text-sm text-gray-300 leading-relaxed whitespace-pre-line mb-6">
            {{ $product->description ?? 'No description available.' }}
        </p>

        <dl class="grid gap-4 sm:grid-cols-2">
            <div class="rounded-lg border border-[#2a3f5f] bg-[#0f1829]/40 p-4">
                <dt class="text-xs text-gray-400 uppercase tracking-widest mb-2 font-semibold">Access Starts</dt>
                <dd class="text-base font-semibold text-white">{{ $entitlement->start_date->format('M j, Y') }}</dd>
            </div>
            <div class="rounded-lg border border-[#2a3f5f] bg-[#0f1829]/40 p-4">
                <dt class="text-xs text-gray-400 uppercase tracking-widest mb-2 font-semibold">Access Ends</dt>
                <dd class="text-base font-semibold text-white">{{ $entitlement->end_date?->format('M j, Y') ?? 'Open ended' }}</dd>
            </div>
        </dl>
    </div>
    </div>

    {{-- Right: Available Downloads ── --}}
    <div class="lg:col-span-1">
    <div class="vd-card  border-[#2a3f5f] !p-6">
        <h2 class="text-lg font-bold text-white mb-5">Entitlement summary</h2>
        <div class="space-y-3">
            @forelse ($downloads as $download)
                <x-download-card
                    :download-item="$download"
                    :download-url="route('user.downloads.download', $download)"
                    compact />
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

    <div class="vd-card  border-[#2a3f5f] !p-6 mt-6">
        <h2 class="text-lg font-bold text-white mb-5">Available Downloads</h2>
        <div class="space-y-3">
            @forelse ($downloads as $download)
                <x-download-card
                    :download-item="$download"
                    :download-url="route('user.downloads.download', $download)"
                    compact />
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

{{-- ── Licenses Table ── --}}
<div class="vd-card  border-[#2a3f5f] overflow-hidden !p-0">
    <div class="border-b border-[#2a3f5f] px-6 py-5">
        <h2 class="text-xl font-bold text-white">Licenses for this Product</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-[#0f1829]/30 border-b border-[#2a3f5f]">
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Activations</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Expiry</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#2a3f5f]">
                @forelse ($licenses as $license)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4 text-sm font-medium text-white">{{ $license->licenseType->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-400">
                            <span class="text-white font-medium">{{ $license->activations->count() }}</span> / {{ $license->max_activations ?? '∞' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-400">
                            {{ $license->expired_date?->format('M j, Y') ?? 'Never' }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('user.licenses.show', $license) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-vd-primary hover:text-vd-primary/80 transition-colors">
                                View →
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-sm text-gray-400">
                            No licenses assigned for this product.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
