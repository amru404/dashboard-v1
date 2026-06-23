@extends('layouts.user')

@section('title', 'My Products')

@section('content')
    <x-page-header
        title="My Products"
        subtitle="Products currently assigned to your account via active entitlements."
    />

    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
        @forelse ($entitlements as $entitlement)
            <div class="vd-card flex flex-col">
                <div class="flex items-start justify-between gap-3 mb-4">
                    <x-badge :active="$entitlement->status === 'active'">{{ ucfirst($entitlement->status) }}</x-badge>
                </div>
                <h2 class="text-label-lg text-vd-on-surface">{{ $entitlement->product->name }}</h2>
                <p class="mt-1 text-eyebrow tracking-[0.14em] text-vd-primary uppercase">{{ $entitlement->product->code }}</p>
                <p class="mt-2 text-body-sm text-vd-muted">{{ $entitlement->product->getCatalogPath() }}</p>

                <dl class="mt-5 flex-1 space-y-2.5 text-body-sm">
                    <div class="flex justify-between gap-4">
                        <dt class="text-vd-muted">Parent</dt>
                        <dd class="text-vd-on-surface font-medium">{{ $entitlement->product->parent?->name ?? 'Root' }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-vd-muted">Access ends</dt>
                        <dd class="text-vd-on-surface font-medium">{{ $entitlement->end_date?->format('M j, Y') ?? 'Open ended' }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-vd-muted">Downloads end</dt>
                        <dd class="text-vd-on-surface font-medium">{{ $entitlement->download_expired_date?->format('M j, Y') ?? 'No limit' }}</dd>
                    </div>
                </dl>

                <div class="mt-5 pt-4 border-t border-vd-border">
                    <a href="{{ route('user.products.show', $entitlement->product) }}" class="vd-btn-secondary w-full justify-center">
                        View Product
                    </a>
                </div>
            </div>
        @empty
            <div class="vd-card md:col-span-2 xl:col-span-3 py-12 text-center">
                <p class="text-body-sm text-vd-muted">No products are currently assigned to your account.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $entitlements->links() }}
    </div>
@endsection
