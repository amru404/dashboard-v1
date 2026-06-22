@extends('layouts.user')

@section('title', 'Customer Dashboard')

@section('content')
    <x-page-header
        title="Customer dashboard"
        subtitle="Your licensed products, active license records, and available downloads."
    />

    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <x-card>
            <p class="text-sm font-semibold text-madani-muted">Owned products</p>
            <p class="mt-3 text-4xl font-extrabold text-madani-deep">{{ $activeEntitlementCount }}</p>
        </x-card>
        <x-card>
            <p class="text-sm font-semibold text-madani-muted">Active licenses</p>
            <p class="mt-3 text-4xl font-extrabold text-madani-deep">{{ $activeLicenseCount }}</p>
        </x-card>
        <x-card>
            <p class="text-sm font-semibold text-madani-muted">Expiring soon</p>
            <p class="mt-3 text-4xl font-extrabold text-madani-deep">{{ $expiringLicenseCount }}</p>
        </x-card>
        <x-card>
            <p class="text-sm font-semibold text-madani-muted">Expired licenses</p>
            <p class="mt-3 text-4xl font-extrabold text-madani-deep">{{ $expiredLicenseCount }}</p>
        </x-card>
    </div>

    <div class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <x-card>
            <p class="text-sm font-semibold text-madani-muted">Downloads</p>
            <p class="mt-3 text-4xl font-extrabold text-madani-deep">{{ $downloadCount }}</p>
        </x-card>
    </div>

    <x-card class="mt-6 overflow-hidden p-0">
        <div class="flex flex-col gap-3 border-b border-madani-border bg-madani-ghost px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-bold text-madani-deep">Owned products</h2>
                <p class="mt-1 text-sm text-madani-muted">Products available through your active entitlements.</p>
            </div>
            <x-button variant="secondary" :href="route('user.products.index')">View products</x-button>
        </div>

        <div class="divide-y divide-madani-border">
            @forelse ($ownedProducts as $product)
                <div class="flex flex-col gap-3 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="font-semibold text-madani-deep">{{ $product->name }}</p>
                        <p class="mt-1 text-sm text-madani-muted">{{ $product->getCatalogPath() }}</p>
                    </div>
                    <x-button variant="ghost" :href="route('user.products.show', $product)">View</x-button>
                </div>
            @empty
                <p class="px-6 py-10 text-center text-sm text-madani-muted">No products are currently assigned to your account.</p>
            @endforelse
        </div>
    </x-card>

    <x-card class="mt-6 overflow-hidden p-0">
        <div class="flex flex-col gap-3 border-b border-madani-border bg-madani-ghost px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-bold text-madani-deep">Available downloads</h2>
                <p class="mt-1 text-sm text-madani-muted">Private files currently available through your entitlements.</p>
            </div>
            <x-button variant="secondary" :href="route('user.downloads.index')">View downloads</x-button>
        </div>

        <div class="divide-y divide-madani-border">
            @forelse ($availableDownloads as $downloadItem)
                <div class="flex flex-col gap-3 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="font-semibold text-madani-deep">{{ $downloadItem->file_name }}</p>
                        <p class="mt-1 text-sm text-madani-muted">{{ $downloadItem->product->getCatalogPath() }} - {{ $downloadItem->version ?? 'No version' }}</p>
                    </div>
                    <x-button variant="ghost" :href="route('user.downloads.download', $downloadItem)">Download</x-button>
                </div>
            @empty
                <p class="px-6 py-10 text-center text-sm text-madani-muted">No downloads are currently available.</p>
            @endforelse
        </div>
    </x-card>

    <x-card class="mt-6 overflow-hidden p-0">
        <div class="flex flex-col gap-3 border-b border-madani-border bg-madani-ghost px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-bold text-madani-deep">Recent licenses</h2>
                <p class="mt-1 text-sm text-madani-muted">{{ Auth::user()->organization?->name ?? 'Unassigned account' }}</p>
            </div>
            <x-button variant="secondary" :href="route('user.licenses.index')">View licenses</x-button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-madani-border">
                <thead class="bg-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Product</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Type</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Expiry</th>
                        <th class="px-6 py-4 text-right text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-madani-border bg-white">
                    @forelse ($recentLicenses as $license)
                        <tr>
                            <td class="px-6 py-4 font-semibold text-madani-deep">{{ $license->product->name }}</td>
                            <td class="px-6 py-4 text-sm text-madani-muted">{{ $license->licenseType->name }}</td>
                            <td class="px-6 py-4 text-sm text-madani-muted">{{ $license->expired_date?->format('M j, Y') ?? 'Never' }}</td>
                            <td class="px-6 py-4 text-right">
                                <x-button variant="ghost" :href="route('user.licenses.show', $license)">View</x-button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-sm text-madani-muted">No licenses are assigned to your account.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <x-card class="mt-6 overflow-hidden p-0">
        <div class="flex flex-col gap-3 border-b border-madani-border bg-madani-ghost px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-bold text-madani-deep">Recent download history</h2>
                <p class="mt-1 text-sm text-madani-muted">Private file downloads recorded for your account.</p>
            </div>
            <x-button variant="secondary" :href="route('user.downloads.index')">View downloads</x-button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-madani-border">
                <thead class="bg-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">File</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Product</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Downloaded</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-madani-border bg-white">
                    @forelse ($recentDownloadLogs as $log)
                        <tr>
                            <td class="px-6 py-4 font-semibold text-madani-deep">{{ $log->downloadItem->file_name }}</td>
                            <td class="px-6 py-4 text-sm text-madani-muted">{{ $log->downloadItem->product->name }}</td>
                            <td class="px-6 py-4 text-sm text-madani-muted">{{ $log->downloaded_at?->format('M j, Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-10 text-center text-sm text-madani-muted">No downloads have been recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
@endsection
