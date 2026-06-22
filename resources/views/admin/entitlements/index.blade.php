@extends('layouts.admin')

@section('title', 'Entitlements')

@section('content')
    <x-page-header title="Entitlements" subtitle="Access grants that control product and download availability separately from installer licenses.">
        <x-slot name="actions">
            <x-button :href="route('admin.entitlements.create')">Grant entitlement</x-button>
        </x-slot>
    </x-page-header>

    <x-card class="mb-6">
        <p class="text-sm leading-6 text-madani-muted">
            Entitlements determine whether a customer can see a product and access related downloads. Licenses are separate installer-facing records.
        </p>
    </x-card>

    <x-card class="overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-madani-border">
                <thead class="bg-madani-ghost">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Customer</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Product</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Access window</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Download window</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-madani-border bg-white">
                    @forelse ($entitlements as $entitlement)
                        <tr>
                            <td class="px-6 py-4">
                                <p class="font-semibold text-madani-deep">{{ $entitlement->user->name }}</p>
                                <p class="mt-1 text-sm text-madani-muted">{{ $entitlement->user->organization?->name ?? 'Unassigned' }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-madani-deep">{{ $entitlement->product->name }}</td>
                            <td class="px-6 py-4 text-sm text-madani-muted">
                                {{ $entitlement->start_date->format('M j, Y') }} to {{ $entitlement->end_date?->format('M j, Y') ?? 'open ended' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-madani-muted">{{ $entitlement->download_expired_date?->format('M j, Y') ?? 'No separate limit' }}</td>
                            <td class="px-6 py-4"><x-badge :active="$entitlement->status === 'active'">{{ ucfirst($entitlement->status) }}</x-badge></td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <x-button variant="ghost" :href="route('admin.entitlements.show', $entitlement)">View</x-button>
                                    <x-button variant="ghost" :href="route('admin.entitlements.edit', $entitlement)">Edit</x-button>
                                    <form method="POST" action="{{ route('admin.entitlements.destroy', $entitlement) }}" onsubmit="return confirm('Delete this entitlement?')">
                                        @csrf
                                        @method('DELETE')
                                        <x-button variant="ghost">Delete</x-button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-sm text-madani-muted">No entitlements have been granted.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-madani-border px-6 py-4">
            {{ $entitlements->links() }}
        </div>
    </x-card>
@endsection
