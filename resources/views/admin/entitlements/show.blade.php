@extends('layouts.admin')

@section('title', 'Entitlement Details')

@section('content')
    <x-page-header title="Entitlement details" subtitle="{{ $entitlement->user->name }} - {{ $entitlement->product->name }}">
        <x-slot name="actions">
            <a href="{{ route('admin.entitlements.index') }}" class="inline-flex items-center px-4 py-2.5 rounded-lg bg-vd-secondary hover:bg-vd-secondary/90 text-white font-semibold text-sm transition-colors">
                Back to entitlements
            </a>
            <x-button :href="route('admin.entitlements.edit', $entitlement)" variant="primary">
                Edit entitlement
            </x-button>
        </x-slot>
    </x-page-header>

    <div class="grid gap-6 lg:grid-cols-[1fr_0.7fr]">
        <x-card>
            <dl class="grid gap-5 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-semibold text-madani-muted">Customer</dt>
                    <dd class="mt-1 text-base font-semibold text-madani-deep">{{ $entitlement->user->name }}</dd>
                    <dd class="mt-1 text-sm text-madani-muted">{{ $entitlement->user->email }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-semibold text-madani-muted">Organization</dt>
                    <dd class="mt-1 text-base text-madani-deep">{{ $entitlement->user->organization?->name ?? 'Unassigned' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-semibold text-madani-muted">Product</dt>
                    <dd class="mt-1 text-base font-semibold text-madani-deep">{{ $entitlement->product->name }}</dd>
                    <dd class="mt-1 text-sm text-madani-muted">{{ $entitlement->product->getCatalogPath() }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-semibold text-madani-muted">Status</dt>
                    <dd class="mt-1"><x-badge :active="$entitlement->status === 'active'">{{ ucfirst($entitlement->status) }}</x-badge></dd>
                </div>
                <div>
                    <dt class="text-sm font-semibold text-madani-muted">Access window</dt>
                    <dd class="mt-1 text-base text-madani-deep">{{ $entitlement->start_date->format('M j, Y') }} to {{ $entitlement->end_date?->format('M j, Y') ?? 'open ended' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-semibold text-madani-muted">Download window</dt>
                    <dd class="mt-1 text-base text-madani-deep">{{ $entitlement->download_expired_date?->format('M j, Y') ?? 'No separate limit' }}</dd>
                </div>
            </dl>
        </x-card>

        <x-card>
            <p class="text-sm font-semibold text-madani-muted">Current download access</p>
            <p class="mt-3 text-4xl font-extrabold text-madani-deep">{{ $entitlement->allowsDownloads() ? 'Allowed' : 'Blocked' }}</p>
            <p class="mt-2 text-sm leading-6 text-madani-muted">Downloads require active entitlement status and valid access/download windows.</p>

            <form method="POST" action="{{ route('admin.entitlements.destroy', $entitlement) }}" class="mt-6" onsubmit="return confirm('Delete this entitlement?')">
                @csrf
                @method('DELETE')
                <x-button variant="danger">Delete entitlement</x-button>
            </form>
        </x-card>
    </div>
@endsection
