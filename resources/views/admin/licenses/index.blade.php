@extends('layouts.admin')

@section('title', 'Licenses')

@section('content')
    <x-page-header title="Licenses" subtitle="Installer-facing license records assigned to customer users.">
        <x-slot name="actions">
            <x-button variant="secondary" :href="route('admin.licenses.batch-create')">Batch issue</x-button>
            <x-button :href="route('admin.licenses.create')">Issue license</x-button>
        </x-slot>
    </x-page-header>

    <x-card class="overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-madani-border">
                <thead class="bg-madani-ghost">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Key</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Customer</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Product</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Type</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Quantity</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Activations</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Expiry</th>
                        <th class="px-6 py-4 text-right text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-madani-border bg-white">
                    @forelse ($licenses as $license)
                        @php
                            $daysUntilExpiry = $license->daysUntilExpiry();
                        @endphp
                        <tr>
                            <td class="px-6 py-4 font-mono text-sm font-semibold text-madani-deep">{{ $license->masked_license_key }}</td>
                            <td class="px-6 py-4">
                                <p class="font-semibold text-madani-deep">{{ $license->user->name }}</p>
                                <p class="mt-1 text-sm text-madani-muted">{{ $license->user->email }}</p>
                                <p class="mt-1 text-xs text-madani-muted">{{ $license->client_name ?? $license->user->organization?->name ?? 'Unassigned' }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-madani-muted">
                                <p class="font-semibold text-madani-deep">{{ $license->product->name }}</p>
                                <p class="mt-1">{{ $license->subProduct?->name ?? 'No sub-product' }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-madani-deep">{{ $license->licenseType->name }}</td>
                            <td class="px-6 py-4 text-sm text-madani-muted">{{ $license->quantity }}</td>
                            <td class="px-6 py-4 text-sm text-madani-muted">
                                {{ $license->active_activations_count }} active / {{ $license->max_activations ?? 'unlimited' }}
                            </td>
                            <td class="px-6 py-4">
                                @if ($license->isExpired())
                                    <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700">Expired</span>
                                @elseif ($daysUntilExpiry !== null && $daysUntilExpiry <= 30)
                                    <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">{{ $daysUntilExpiry }} days left</span>
                                @else
                                    <span class="rounded-full bg-madani-pale px-3 py-1 text-xs font-semibold text-madani-green">{{ $license->expired_date ? 'Active' : 'No expiry' }}</span>
                                @endif
                                <p class="mt-2 text-xs text-madani-muted">{{ $license->expired_date?->format('M j, Y') ?? 'Never' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <x-button variant="ghost" :href="route('admin.licenses.show', $license)">View</x-button>
                                    <x-button variant="ghost" :href="route('admin.licenses.edit', $license)">Edit</x-button>
                                    <form method="POST" action="{{ route('admin.licenses.destroy', $license) }}" onsubmit="return confirm('Delete this license? This will also remove related activation records.')">
                                        @csrf
                                        @method('DELETE')
                                        <x-button variant="ghost">Delete</x-button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-sm text-madani-muted">No licenses have been issued.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-madani-border px-6 py-4">
            {{ $licenses->links() }}
        </div>
    </x-card>
@endsection
