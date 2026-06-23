@extends('layouts.user')

@section('title', $product->name)

@section('content')
    <x-page-header title="{{ $product->name }}" subtitle="{{ $product->getCatalogPath() }}">
        <x-slot name="actions">
            <x-button variant="secondary" :href="route('user.products.index')">Back to Products</x-button>
        </x-slot>
    </x-page-header>

    <x-product-breadcrumbs
        :breadcrumbs="$breadcrumbs"
        :current="$product"
        route-name="user.products.show"
        :link-items="false"
        :path="$product->getCatalogPath()"
    />

    <div class="grid gap-6 lg:grid-cols-[1fr_0.65fr]">

        {{-- Product details --}}
        <div class="vd-card">
            <div class="flex items-start justify-between gap-3 mb-4">
                <x-badge :active="$product->is_active" />
            </div>
            <h2 class="text-label-lg text-vd-on-surface mb-3">Product Details</h2>
            <p class="text-body-sm text-vd-muted leading-relaxed whitespace-pre-line">
                {{ $product->description ?? 'No description available.' }}
            </p>

            <dl class="mt-6 grid gap-4 sm:grid-cols-2">
                <div class="rounded-lg border border-vd-border bg-vd-secondary/40 p-4">
                    <dt class="text-eyebrow text-vd-muted uppercase tracking-widest mb-2">Access Starts</dt>
                    <dd class="text-label-md text-vd-on-surface">{{ $entitlement->start_date->format('M j, Y') }}</dd>
                </div>
                <div class="rounded-lg border border-vd-border bg-vd-secondary/40 p-4">
                    <dt class="text-eyebrow text-vd-muted uppercase tracking-widest mb-2">Access Ends</dt>
                    <dd class="text-label-md text-vd-on-surface">{{ $entitlement->end_date?->format('M j, Y') ?? 'Open ended' }}</dd>
                </div>
            </dl>
        </div>

        {{-- Downloads --}}
        <div class="vd-card">
            <h2 class="text-label-lg text-vd-on-surface mb-5">Available Downloads</h2>
            <div class="space-y-3">
                @forelse ($downloads as $download)
                    <x-download-card
                        :download-item="$download"
                        :download-url="route('user.downloads.download', $download)"
                        compact />
                @empty
                    <p class="text-body-sm text-vd-muted">No downloads available for this product.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Licenses table --}}
    <div class="vd-card overflow-hidden !p-0 mt-6">
        <div class="border-b border-vd-border px-6 py-5">
            <h2 class="text-label-lg text-vd-on-surface">Licenses for this Product</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="vd-table">
                <thead class="bg-vd-surface">
                    <tr>
                        <th class="vd-thead">Type</th>
                        <th class="vd-thead">Activations</th>
                        <th class="vd-thead">Expiry</th>
                        <th class="px-6 py-4 text-right text-eyebrow text-vd-muted tracking-widest">Action</th>
                    </tr>
                </thead>
                <tbody class="vd-tbody">
                    @forelse ($licenses as $license)
                        <tr>
                            <td class="px-6 py-4 text-label-md text-vd-on-surface">{{ $license->licenseType->name }}</td>
                            <td class="px-6 py-4 text-body-sm text-vd-muted">
                                {{ $license->activations->count() }} / {{ $license->max_activations ?? '∞' }}
                            </td>
                            <td class="px-6 py-4 text-body-sm text-vd-muted">
                                {{ $license->expired_date?->format('M j, Y') ?? 'Never' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('user.licenses.show', $license) }}" class="vd-btn-ghost">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-body-sm text-vd-muted">
                                No licenses assigned for this product.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
