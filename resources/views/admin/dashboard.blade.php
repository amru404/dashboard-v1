@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
{{-- ── Page header ── --}}
<div class="mb-8">
    <p class="text-eyebrow tracking-[0.18em] text-vd-primary uppercase mb-2">Admin Console</p>
    @auth
        <h1 class="text-headline-md text-vd-on-surface">Welcome back, {{ auth()->user()->name }}</h1>
    @endauth
    <p class="mt-1 text-body-sm text-vd-muted">
        Monitor organizations, users, products, and licenses from a single control panel.
    </p>
</div>

{{-- ── Stat cards row ── --}}
<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-8">
    @php
        $stats = [
            ['label' => 'Organizations', 'value' => $organizationCount, 'sub' => $activeOrganizationCount . ' active',  'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'color' => 'text-vd-accent-cyan'],
            ['label' => 'Users',         'value' => $userCount,         'sub' => $activeUserCount . ' active',          'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0', 'color' => 'text-vd-primary'],
            ['label' => 'Products',      'value' => $productCount,      'sub' => $activeProductCount . ' active',       'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'color' => 'text-vd-accent-magenta'],
            ['label' => 'Licenses',      'value' => $licenseCount,      'sub' => $expiringLicenseCount . ' expiring',  'icon' => 'M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z', 'color' => 'text-vd-success'],
        ];
    @endphp

    @foreach ($stats as $stat)
        <div class="vd-card flex items-start gap-4">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-md bg-white/5">
                <svg class="h-5 w-5 {{ $stat['color'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $stat['icon'] }}"/>
                </svg>
            </div>
            <div>
                <p class="text-label-sm text-vd-muted">{{ $stat['label'] }}</p>
                <p class="mt-1 text-headline-sm text-vd-on-surface">{{ $stat['value'] }}</p>
                <p class="mt-0.5 text-body-sm text-vd-muted">{{ $stat['sub'] }}</p>
            </div>
        </div>
    @endforeach
</div>

{{-- ── Quick access cards ── --}}
<h2 class="text-label-lg text-vd-muted mb-4 uppercase tracking-widest text-[11px]">Quick Access</h2>
<div class="grid gap-4 lg:grid-cols-3 mb-8">
    @php
        $quickLinks = [
            ['title' => 'Products',      'desc' => 'Build and manage the recursive product catalog used by licenses, entitlements, and downloads.', 'route' => 'admin.products.index',      'label' => 'View Products'],
            ['title' => 'Licenses',      'desc' => 'Review issued license records, activation usage, and manage sub-product key assignments.', 'route' => 'admin.licenses.index',       'label' => 'View Licenses'],
            ['title' => 'Entitlements',  'desc' => 'Control customer product access and download windows through entitlement grants.', 'route' => 'admin.entitlements.index',   'label' => 'View Entitlements'],
            ['title' => 'Downloads',     'desc' => 'Manage private installer files delivered through entitlement-checked streaming.', 'route' => 'admin.download-items.index', 'label' => 'View Downloads'],
            ['title' => 'Organizations', 'desc' => 'Group customers and assign billing identities across your license base.', 'route' => 'admin.organizations.index',  'label' => 'View Orgs'],
            ['title' => 'Users',         'desc' => 'Manage admin and customer accounts with role, organization, and status controls.', 'route' => 'admin.users.index',          'label' => 'View Users'],
        ];
    @endphp

    @foreach ($quickLinks as $ql)
        <div class="vd-card flex flex-col gap-3">
            <div class="flex-1">
                <h3 class="text-label-lg text-vd-on-surface">{{ $ql['title'] }}</h3>
                <p class="mt-2 text-body-sm text-vd-muted leading-relaxed">{{ $ql['desc'] }}</p>
            </div>
            <div>
                <a href="{{ route($ql['route']) }}" class="vd-btn-secondary text-body-sm">
                    {{ $ql['label'] }}
                </a>
            </div>
        </div>
    @endforeach
</div>
@endsection
