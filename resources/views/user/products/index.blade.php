@extends('layouts.user')

@section('title', 'My Products')

@section('content')
    <x-page-header title="My products" subtitle="Products currently assigned to your account." />

    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
        @forelse ($entitlements as $entitlement)
            <x-card>
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-bold text-madani-deep">{{ $entitlement->product->name }}</h2>
                        <p class="mt-1 text-sm font-semibold uppercase tracking-[0.14em] text-madani-muted">{{ $entitlement->product->code }}</p>
                        <p class="mt-2 text-sm text-madani-muted">{{ $entitlement->product->getCatalogPath() }}</p>
                    </div>
                    <x-badge :active="$entitlement->status === 'active'">{{ ucfirst($entitlement->status) }}</x-badge>
                </div>
                <dl class="mt-5 space-y-3 text-sm">
                    <div class="flex justify-between gap-4">
                        <dt class="text-madani-muted">Parent</dt>
                        <dd class="font-semibold text-madani-deep">{{ $entitlement->product->parent?->name ?? 'Root product' }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-madani-muted">Access ends</dt>
                        <dd class="font-semibold text-madani-deep">{{ $entitlement->end_date?->format('M j, Y') ?? 'Open ended' }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-madani-muted">Downloads end</dt>
                        <dd class="font-semibold text-madani-deep">{{ $entitlement->download_expired_date?->format('M j, Y') ?? 'No separate limit' }}</dd>
                    </div>
                </dl>
                <div class="mt-6">
                    <x-button variant="secondary" :href="route('user.products.show', $entitlement->product)">View product</x-button>
                </div>
            </x-card>
        @empty
            <x-card class="md:col-span-2 xl:col-span-3">
                <p class="text-sm text-madani-muted">No products are currently assigned to your account.</p>
            </x-card>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $entitlements->links() }}
    </div>
@endsection
