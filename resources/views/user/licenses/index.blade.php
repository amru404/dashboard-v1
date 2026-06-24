@extends('layouts.user')

@section('title', 'My Licenses')

@section('content')

{{-- ── Page Header ── --}}
<div class="mb-8">
    <h1 class="text-3xl font-bold text-white mb-2">Licenses</h1>
    <p class="text-base text-gray-300">
        License records assigned to your account. Parent licenses group your sub-product keys.
    </p>
</div>

{{-- ── Group licenses by parent product ── --}}
@php
    $groupedLicenses = $licenses->groupBy(function($license) {
        return $license->product_id;
    });
@endphp

<div class="space-y-6">
    @forelse ($groupedLicenses as $productId => $productLicenses)
        @php
            $parentLicense = $productLicenses->first();
            $product = $parentLicense->product;
            $subProductLicenses = $productLicenses->filter(fn($l) => $l->sub_product_id !== null);
            $parentOnlyLicense = $productLicenses->firstWhere('is_parent_only', true);
        @endphp
        
        {{-- Product Card with Sub-Product Table ── --}}
        <div class="vd-card  border-[#2a3f5f] !p-0 overflow-hidden">
            
            {{-- Card Header --}}
            <div class="px-6 py-5 border-b border-[#2a3f5f]">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h2 class="text-xl font-bold text-white">{{ $product->name }}</h2>
                            @if ($parentOnlyLicense)
                                @php $days = $parentOnlyLicense->daysUntilExpiry(); @endphp
                                @if ($parentOnlyLicense->isExpired())
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-500/20 text-red-400 border border-red-500/30">
                                        Expired
                                    </span>
                                @elseif ($days !== null && $days <= 30)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-orange-500/20 text-orange-400 border border-orange-500/30">
                                        ⏱ Expiring Soon
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-500/20 text-blue-400 border border-blue-500/30">
                                        ● Active
                                    </span>
                                @endif
                            @endif
                        </div>
                        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-400">
                            <span>Organization: {{ Auth::user()->organization?->name ?? '-' }}</span>
                            <span>·</span>
                            <span>{{ $subProductLicenses->count() }} {{ Str::plural('module', $subProductLicenses->count()) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Module Pills Section --}}
            @if ($subProductLicenses->count() > 0)
                <div class="px-6 py-4 bg-[#0f1829]/50 border-b border-[#2a3f5f]">
                    <div class="flex flex-wrap gap-2">
                        @foreach ($subProductLicenses->take(10) as $subLicense)
                            <span class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-medium bg-[#1a2942] text-gray-300 border border-[#2a3f5f]">
                                {{ $subLicense->subProduct->name ?? $subLicense->licenseType->name }}
                            </span>
                        @endforeach
                        @if ($subProductLicenses->count() > 10)
                            <span class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-medium bg-[#1a2942] text-gray-400 border border-[#2a3f5f]">
                                +{{ $subProductLicenses->count() - 10 }} more
                            </span>
                        @endif
                    </div>
                </div>
            @endif
            
            {{-- Sub-Products Table --}}
            @if ($subProductLicenses->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-[#0f1829]/30 border-b border-[#2a3f5f]">
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Module</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">License Key</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Activations</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Expiry</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#2a3f5f]">
                            @foreach ($subProductLicenses as $license)
                                <tr class="hover:bg-white/5 transition-colors">
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-medium text-white">
                                            {{ $license->subProduct->name ?? $license->licenseType->name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-mono text-gray-300">{{ $license->masked_license_key }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm text-gray-400">{{ $license->licenseType->name }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm text-gray-400">
                                            <span class="text-white font-medium">{{ $license->active_activations_count }}</span> / {{ $license->max_activations ?? '∞' }}
                                        </span>
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
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-6 py-8 text-center">
                    <p class="text-sm text-gray-400">No sub-product licenses available</p>
                </div>
            @endif
        </div>
        
    @empty
        <div class="vd-card  border-[#2a3f5f] !p-12 text-center">
            <svg class="mx-auto mb-4 h-12 w-12 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
            </svg>
            <p class="text-gray-400 mb-4">No licenses are assigned to your account.</p>
        </div>
    @endforelse
</div>

{{-- ── Pagination ── --}}
@if ($licenses->hasPages())
    <div class="mt-8">
        {{ $licenses->links() }}
    </div>
@endif

@endsection
