@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
    <x-page-header
        title="Admin dashboard"
        subtitle="Monitor the protected admin area for organizations, users, products, and licenses."
    />

    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <x-card>
            <p class="text-sm font-semibold text-madani-muted">Organizations</p>
            <p class="mt-3 text-4xl font-extrabold text-madani-deep">{{ $organizationCount }}</p>
            <p class="mt-2 text-sm text-madani-muted">{{ $activeOrganizationCount }} active</p>
        </x-card>

        <x-card>
            <p class="text-sm font-semibold text-madani-muted">Users</p>
            <p class="mt-3 text-4xl font-extrabold text-madani-deep">{{ $userCount }}</p>
            <p class="mt-2 text-sm text-madani-muted">{{ $activeUserCount }} active</p>
        </x-card>

        <x-card>
            <p class="text-sm font-semibold text-madani-muted">Products</p>
            <p class="mt-3 text-4xl font-extrabold text-madani-deep">{{ $productCount }}</p>
            <p class="mt-2 text-sm text-madani-muted">{{ $activeProductCount }} active</p>
        </x-card>

        <x-card>
            <p class="text-sm font-semibold text-madani-muted">Licenses</p>
            <p class="mt-3 text-4xl font-extrabold text-madani-deep">{{ $licenseCount }}</p>
            <p class="mt-2 text-sm text-madani-muted">{{ $expiringLicenseCount }} expiring soon</p>
        </x-card>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        <x-card>
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold text-madani-deep">Products</h2>
                    <p class="mt-2 text-sm leading-6 text-madani-muted">Build the software catalog that licenses, entitlements, and downloads attach to.</p>
                </div>
                <x-button :href="route('admin.products.index')">View products</x-button>
            </div>
        </x-card>

        <x-card>
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold text-madani-deep">Licenses</h2>
                    <p class="mt-2 text-sm leading-6 text-madani-muted">Review issued license records and activation usage.</p>
                </div>
                <x-button :href="route('admin.licenses.index')">View licenses</x-button>
            </div>
        </x-card>
    </div>
@endsection
