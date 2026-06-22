@extends('layouts.user')

@section('title', $product->name)

@section('content')
    <x-page-header title="{{ $product->name }}" subtitle="{{ $product->getCatalogPath() }}">
        <x-slot name="actions">
            <x-button variant="secondary" :href="route('user.products.index')">Back to products</x-button>
        </x-slot>
    </x-page-header>

    <x-product-breadcrumbs
        :breadcrumbs="$breadcrumbs"
        :current="$product"
        route-name="user.products.show"
        :link-items="false"
        :path="$product->getCatalogPath()"
    />

    <div class="grid gap-6 lg:grid-cols-[1fr_0.7fr]">
        <x-card>
            <x-badge :active="$product->is_active" />
            <h2 class="mt-4 text-xl font-bold text-madani-deep">Product details</h2>
            <p class="mt-3 whitespace-pre-line text-sm leading-7 text-madani-muted">{{ $product->description ?? 'No description available.' }}</p>

            <dl class="mt-6 grid gap-4 sm:grid-cols-2">
                <div class="rounded-xl border border-madani-border bg-madani-ghost p-4">
                    <dt class="text-xs font-semibold uppercase tracking-[0.16em] text-madani-muted">Access starts</dt>
                    <dd class="mt-2 text-sm font-semibold text-madani-deep">{{ $entitlement->start_date->format('M j, Y') }}</dd>
                </div>
                <div class="rounded-xl border border-madani-border bg-madani-ghost p-4">
                    <dt class="text-xs font-semibold uppercase tracking-[0.16em] text-madani-muted">Access ends</dt>
                    <dd class="mt-2 text-sm font-semibold text-madani-deep">{{ $entitlement->end_date?->format('M j, Y') ?? 'Open ended' }}</dd>
                </div>
            </dl>
        </x-card>

        <x-card>
            <h2 class="text-lg font-bold text-madani-deep">Available downloads</h2>
            <div class="mt-5 space-y-3">
                @forelse ($downloads as $download)
                    <x-download-card :download-item="$download" :download-url="route('user.downloads.download', $download)" compact />
                @empty
                    <p class="text-sm text-madani-muted">No downloads are available for this product.</p>
                @endforelse
            </div>
        </x-card>
    </div>

    <x-card class="mt-6 overflow-hidden p-0">
        <div class="border-b border-madani-border bg-madani-ghost px-6 py-5">
            <h2 class="text-lg font-bold text-madani-deep">Licenses for this product</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-madani-border">
                <thead class="bg-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Type</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Activations</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Expiry</th>
                        <th class="px-6 py-4 text-right text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-madani-border bg-white">
                    @forelse ($licenses as $license)
                        <tr>
                            <td class="px-6 py-4 text-sm font-semibold text-madani-deep">{{ $license->licenseType->name }}</td>
                            <td class="px-6 py-4 text-sm text-madani-muted">{{ $license->activations->count() }} / {{ $license->max_activations ?? 'unlimited' }}</td>
                            <td class="px-6 py-4 text-sm text-madani-muted">{{ $license->expired_date?->format('M j, Y') ?? 'Never' }}</td>
                            <td class="px-6 py-4 text-right">
                                <x-button variant="ghost" :href="route('user.licenses.show', $license)">View</x-button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-sm text-madani-muted">No licenses are assigned for this product.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
@endsection
