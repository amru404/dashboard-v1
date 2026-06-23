@extends('layouts.user')

@section('title', 'Dashboard')

@section('content')

{{-- ── Page header ── --}}
<div class="mb-8">
    <p class="text-eyebrow tracking-[0.18em] text-vd-primary uppercase mb-2">Customer Portal</p>
    @auth
        <h1 class="text-headline-md text-vd-on-surface">Welcome back, {{ auth()->user()->name }}</h1>
    @endauth
    <p class="mt-1 text-body-sm text-vd-muted">
        Your licensed products, active license records, and available downloads — all in one place.
    </p>
</div>

{{-- ── Stat cards ── --}}
<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-8">
    @php
        $stats = [
            ['label' => 'Owned Products',    'value' => $activeEntitlementCount, 'color' => 'text-vd-accent-cyan'],
            ['label' => 'Active Licenses',   'value' => $activeLicenseCount,     'color' => 'text-vd-success'],
            ['label' => 'Expiring Soon',     'value' => $expiringLicenseCount,   'color' => 'text-vd-warning'],
            ['label' => 'Expired Licenses',  'value' => $expiredLicenseCount,    'color' => 'text-vd-error'],
        ];
    @endphp

    @foreach ($stats as $stat)
        <div class="vd-card">
            <p class="text-label-sm text-vd-muted">{{ $stat['label'] }}</p>
            <p class="mt-2 text-headline-sm {{ $stat['color'] }}">{{ $stat['value'] }}</p>
        </div>
    @endforeach
</div>

{{-- ── Downloads available ── --}}
<div class="vd-card mb-4">
    <p class="text-label-sm text-vd-muted">Available Downloads</p>
    <p class="mt-2 text-headline-sm text-vd-accent-magenta">{{ $downloadCount }}</p>
</div>

{{-- ── Owned Products ── --}}
<div class="vd-card overflow-hidden !p-0 mb-6">
    <div class="flex flex-col gap-3 border-b border-vd-border px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-label-lg text-vd-on-surface">Owned Products</h2>
            <p class="mt-1 text-body-sm text-vd-muted">Products available through your active entitlements.</p>
        </div>
        <a href="{{ route('user.products.index') }}" class="vd-btn-secondary shrink-0">View All</a>
    </div>
    <div class="divide-y divide-vd-border">
        @forelse ($ownedProducts as $product)
            <div class="flex flex-col gap-3 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-label-md text-vd-on-surface">{{ $product->name }}</p>
                    <p class="mt-1 text-body-sm text-vd-muted">{{ $product->getCatalogPath() }}</p>
                </div>
                <a href="{{ route('user.products.show', $product) }}" class="vd-btn-ghost shrink-0">View</a>
            </div>
        @empty
            <p class="px-6 py-10 text-center text-body-sm text-vd-muted">No products are currently assigned to your account.</p>
        @endforelse
    </div>
</div>

{{-- ── Available Downloads ── --}}
<div class="vd-card overflow-hidden !p-0 mb-6">
    <div class="flex flex-col gap-3 border-b border-vd-border px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-label-lg text-vd-on-surface">Available Downloads</h2>
            <p class="mt-1 text-body-sm text-vd-muted">Private files currently available through your entitlements.</p>
        </div>
        <a href="{{ route('user.downloads.index') }}" class="vd-btn-secondary shrink-0">View All</a>
    </div>
    <div class="divide-y divide-vd-border">
        @forelse ($availableDownloads as $downloadItem)
            <div class="flex flex-col gap-3 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-label-md text-vd-on-surface">{{ $downloadItem->file_name }}</p>
                    <p class="mt-1 text-body-sm text-vd-muted">
                        {{ $downloadItem->product->getCatalogPath() }}
                        @if ($downloadItem->version) &mdash; v{{ $downloadItem->version }} @endif
                    </p>
                </div>
                <a href="{{ route('user.downloads.download', $downloadItem) }}" class="vd-btn-primary shrink-0">
                    Download
                </a>
            </div>
        @empty
            <p class="px-6 py-10 text-center text-body-sm text-vd-muted">No downloads are currently available.</p>
        @endforelse
    </div>
</div>

{{-- ── Recent Licenses ── --}}
<div class="vd-card overflow-hidden !p-0 mb-6">
    <div class="flex flex-col gap-3 border-b border-vd-border px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-label-lg text-vd-on-surface">Recent Licenses</h2>
            <p class="mt-1 text-body-sm text-vd-muted">{{ Auth::user()->organization?->name ?? 'Unassigned account' }}</p>
        </div>
        <a href="{{ route('user.licenses.index') }}" class="vd-btn-secondary shrink-0">View All</a>
    </div>
    <div class="overflow-x-auto">
        <table class="vd-table">
            <thead class="bg-vd-surface">
                <tr>
                    <th class="vd-thead">Product</th>
                    <th class="vd-thead">Type</th>
                    <th class="vd-thead">Expiry</th>
                    <th class="px-6 py-4 text-right text-eyebrow text-vd-muted tracking-widest">Action</th>
                </tr>
            </thead>
            <tbody class="vd-tbody">
                @forelse ($recentLicenses as $license)
                    <tr>
                        <td class="px-6 py-4 text-label-md text-vd-on-surface">{{ $license->product->name }}</td>
                        <td class="px-6 py-4 text-body-sm text-vd-muted">{{ $license->licenseType->name }}</td>
                        <td class="px-6 py-4 text-body-sm text-vd-muted">{{ $license->expired_date?->format('M j, Y') ?? 'Never' }}</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('user.licenses.show', $license) }}" class="vd-btn-ghost">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-body-sm text-vd-muted">No licenses are assigned to your account.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ── Recent Download History ── --}}
<div class="vd-card overflow-hidden !p-0">
    <div class="flex flex-col gap-3 border-b border-vd-border px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-label-lg text-vd-on-surface">Recent Download History</h2>
            <p class="mt-1 text-body-sm text-vd-muted">Private file downloads recorded for your account.</p>
        </div>
        <a href="{{ route('user.downloads.index') }}" class="vd-btn-secondary shrink-0">View Downloads</a>
    </div>
    <div class="overflow-x-auto">
        <table class="vd-table">
            <thead class="bg-vd-surface">
                <tr>
                    <th class="vd-thead">File</th>
                    <th class="vd-thead">Product</th>
                    <th class="vd-thead">Downloaded</th>
                </tr>
            </thead>
            <tbody class="vd-tbody">
                @forelse ($recentDownloadLogs as $log)
                    <tr>
                        <td class="px-6 py-4 text-label-md text-vd-on-surface">{{ $log->downloadItem->file_name }}</td>
                        <td class="px-6 py-4 text-body-sm text-vd-muted">{{ $log->downloadItem->product->name }}</td>
                        <td class="px-6 py-4 text-body-sm text-vd-muted">{{ $log->downloaded_at?->format('M j, Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-10 text-center text-body-sm text-vd-muted">No downloads have been recorded yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
