@extends('layouts.admin')

@section('title', 'Entitlements')

@section('content')
    <x-page-header
        title="Entitlements"
        subtitle="Access grants that control product visibility and download availability — separate from installer licenses."
    >
        <x-slot name="actions">
            <x-button :href="route('admin.entitlements.create')">Grant Entitlement</x-button>
        </x-slot>
    </x-page-header>

    <div class="vd-card mb-6 flex items-start gap-3">
        <svg class="mt-0.5 h-4 w-4 shrink-0 text-vd-accent-cyan" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-body-sm text-vd-muted leading-relaxed">
            Entitlements determine whether a customer can see a product and access related downloads. They cascade to sub-products. Licenses are separate installer-facing records.
        </p>
    </div>

    <div class="vd-card overflow-hidden !p-0">
        <div class="overflow-x-auto">
            <table class="vd-table">
                <thead class="bg-vd-surface">
                    <tr>
                        <th class="vd-thead">Customer</th>
                        <th class="vd-thead">Product</th>
                        <th class="vd-thead">Access Window</th>
                        <th class="vd-thead">Download Window</th>
                        <th class="vd-thead">Status</th>
                        <th class="px-6 py-4 text-right text-eyebrow text-vd-muted tracking-widest">Actions</th>
                    </tr>
                </thead>
                <tbody class="vd-tbody">
                    @forelse ($entitlements as $entitlement)
                        <tr>
                            <td class="px-6 py-4">
                                <p class="text-label-md text-vd-on-surface">{{ $entitlement->user->name }}</p>
                                <p class="mt-1 text-body-sm text-vd-muted">
                                    {{ $entitlement->user->organization?->name ?? 'Unassigned' }}
                                </p>
                            </td>
                            <td class="px-6 py-4 text-label-md text-vd-on-surface">{{ $entitlement->product->name }}</td>
                            <td class="px-6 py-4 text-body-sm text-vd-muted">
                                {{ $entitlement->start_date->format('M j, Y') }}
                                &rarr;
                                {{ $entitlement->end_date?->format('M j, Y') ?? 'open ended' }}
                            </td>
                            <td class="px-6 py-4 text-body-sm text-vd-muted">
                                {{ $entitlement->download_expired_date?->format('M j, Y') ?? 'No separate limit' }}
                            </td>
                            <td class="px-6 py-4">
                                <x-badge :active="$entitlement->status === 'active'">{{ ucfirst($entitlement->status) }}</x-badge>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <x-button variant="ghost" :href="route('admin.entitlements.show', $entitlement)">View</x-button>
                                    <x-button variant="ghost" :href="route('admin.entitlements.edit', $entitlement)">Edit</x-button>
                                    <form method="POST" action="{{ route('admin.entitlements.destroy', $entitlement) }}"
                                          onsubmit="return confirm('Delete this entitlement?')">
                                        @csrf @method('DELETE')
                                        <x-button variant="danger">Delete</x-button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-body-sm text-vd-muted">
                                No entitlements have been granted.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-vd-border px-6 py-4">
            {{ $entitlements->links() }}
        </div>
    </div>
@endsection
