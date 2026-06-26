@extends('layouts.user')

@section('title', 'My Licenses')

@section('content')

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

{{-- License Tree --}}
<x-license-tree :products="$rootProducts" />

{{-- No Results Message (for filtering) --}}
<div id="noLicenseResults" class="hidden vd-card border-[#2a3f5f] !p-12 text-center">
    <svg class="mx-auto mb-4 h-12 w-12 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
    </svg>
    <p class="text-gray-400">No licenses found matching your search criteria.</p>
</div>

@if ($rootProducts->hasPages())
    <div class="mt-8">
        {{ $rootProducts->links() }}
    </div>
@endif

@endsection

@vite('resources/js/license-filter.js')
