@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')

{{-- ── Hero Welcome Card ── --}}
<div class="vd-card mb-8 !p-8 md:!p-12 border-[#2a3f5f]">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
        <div class="flex-1">
            <p class="text-eyebrow tracking-[0.18em] text-vd-primary uppercase mb-3">ADMIN CONSOLE</p>
            @auth
                <h1 class="text-3xl md:text-4xl font-bold text-white mb-4">Welcome back, {{ auth()->user()->name }}</h1>
            @endauth
            <p class="text-base text-gray-300 leading-relaxed mb-6 max-w-2xl">
                Monitor organizations, users, products, and licenses from a single control panel.
            </p>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.licenses.index') }}" class="inline-flex items-center justify-center px-6 py-3 rounded-lg bg-vd-primary hover:bg-vd-primary/90 text-white font-semibold text-sm transition-colors">
                    Manage Licenses
                </a>
                <a href="{{ route('admin.products.index') }}" class="inline-flex items-center justify-center px-6 py-3 rounded-lg bg-white/10 hover:bg-white/15 text-white font-semibold text-sm border border-white/20 transition-colors">
                    Products
                </a>
            </div>
        </div>
        
    </div>
</div>

{{-- ── Stats Row ── --}}
<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-8">
    @php
        $stats = [
            [
                'label' => 'Organizations',
                'value' => $organizationCount,
                'sublabel' => $activeOrganizationCount . ' active',
                'color' => 'text-cyan-400',
                'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'
            ],
            [
                'label' => 'Users',
                'value' => $userCount,
                'sublabel' => $activeUserCount . ' active',
                'color' => 'text-blue-400',
                'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0'
            ],
            [
                'label' => 'Products',
                'value' => $productCount,
                'sublabel' => $activeProductCount . ' active',
                'color' => 'text-purple-400',
                'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'
            ],
            [
                'label' => 'Licenses',
                'value' => $licenseCount,
                'sublabel' => $expiringLicenseCount . ' expiring soon',
                'color' => 'text-green-400',
                'icon' => 'M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z'
            ],
        ];
    @endphp

    @foreach ($stats as $stat)
       <div class="vd-card border-[#2a3f5f]">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wider mb-2 font-semibold">
                    {{ $stat['label'] }}
                </p>
                <p class="text-3xl font-bold mb-1">
                    {{ $stat['value'] }}
                </p>
                <p class="text-sm text-gray-400">
                    {{ $stat['sublabel'] }}
                </p>
            </div>

            <div class="ml-4 {{ $stat['color'] }}">
                <svg class="h-6 w-6 {{ $stat['color'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $stat['icon'] }}"/>
                </svg>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- ── Quick Access Grid ── --}}
<h2 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Quick Access</h2>
<div class="grid gap-4 lg:grid-cols-3 mb-8">
    @php
        $quickLinks = [
            [
                'title' => 'Products',
                'desc' => 'Build and manage the recursive product catalog used by licenses, entitlements, and downloads.',
                'route' => 'admin.products.index',
                'label' => 'View Products',
                'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
                'color' => 'text-purple-400',
                'bg' => 'bg-purple-500/20'
            ],
            [
                'title' => 'Licenses',
                'desc' => 'Review issued license records, activation usage, and manage sub-product key assignments.',
                'route' => 'admin.licenses.index',
                'label' => 'View Licenses',
                'icon' => 'M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z',
                'color' => 'text-green-400',
                'bg' => 'bg-green-500/20'
            ],
            [
                'title' => 'Entitlements',
                'desc' => 'Control customer product access and download windows through entitlement grants.',
                'route' => 'admin.entitlements.index',
                'label' => 'View Entitlements',
                'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
                'color' => 'text-blue-400',
                'bg' => 'bg-blue-500/20'
            ],
            [
                'title' => 'Downloads',
                'desc' => 'Manage private installer files delivered through entitlement-checked streaming.',
                'route' => 'admin.download-items.index',
                'label' => 'View Downloads',
                'icon' => 'M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10',
                'color' => 'text-cyan-400',
                'bg' => 'bg-cyan-500/20'
            ],
            [
                'title' => 'Organizations',
                'desc' => 'Group customers and assign billing identities across your license base.',
                'route' => 'admin.organizations.index',
                'label' => 'View Organizations',
                'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                'color' => 'text-orange-400',
                'bg' => 'bg-orange-500/20'
            ],
            [
                'title' => 'Users',
                'desc' => 'Manage admin and customer accounts with role, organization, and status controls.',
                'route' => 'admin.users.index',
                'label' => 'View Users',
                'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0',
                'color' => 'text-pink-400',
                'bg' => 'bg-pink-500/20'
            ],
        ];
    @endphp

    @foreach ($quickLinks as $ql)
        <div class="vd-card  border-[#2a3f5f] flex flex-col">
            <div class="flex items-start gap-4 mb-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg {{ $ql['bg'] }} border border-[#2a3f5f]">
                    <svg class="h-6 w-6 {{ $ql['color'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $ql['icon'] }}"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-white">{{ $ql['title'] }}</h3>
                </div>
            </div>
            <p class="text-sm text-gray-300 leading-relaxed mb-5 flex-1">{{ $ql['desc'] }}</p>
            <div>
                <a href="{{ route($ql['route']) }}" class="inline-flex items-center justify-center w-full px-4 py-2.5 rounded-lg bg-white/10 hover:bg-white/15 text-white font-semibold text-sm border border-white/20 transition-colors">
                    {{ $ql['label'] }} →
                </a>
            </div>
        </div>
    @endforeach
</div>

@endsection
