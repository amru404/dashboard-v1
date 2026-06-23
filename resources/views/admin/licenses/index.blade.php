@extends('layouts.admin')

@section('title', 'Licenses')

@section('content')
    <x-page-header
        title="Licenses"
        subtitle="Installer-facing license records assigned to customer users. Parent licenses act as containers; actual keys live at the sub-product level."
    >
        <x-slot name="actions">
            <x-button variant="secondary" :href="route('admin.licenses.batch-create')">Batch Issue</x-button>
            <x-button :href="route('admin.licenses.create')">Issue License</x-button>
        </x-slot>
    </x-page-header>

    <div class="vd-card overflow-hidden !p-0">
        <div class="overflow-x-auto">
            <table class="vd-table">
                <thead class="bg-vd-surface">
                    <tr>
                        <th class="vd-thead">Key</th>
                        <th class="vd-thead">Customer</th>
                        <th class="vd-thead">Product</th>
                        <th class="vd-thead">Type</th>
                        <th class="vd-thead">Qty</th>
                        <th class="vd-thead">Activations</th>
                        <th class="vd-thead">Expiry</th>
                        <th class="px-6 py-4 text-right text-eyebrow text-vd-muted tracking-widest">Actions</th>
                    </tr>
                </thead>
                <tbody class="vd-tbody">
                    @forelse ($licenses as $license)
                        @php $days = $license->daysUntilExpiry(); @endphp
                        <tr>
                            <td class="px-6 py-4 font-mono text-body-sm text-vd-on-surface">
                                {{ $license->masked_license_key }}
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-label-md text-vd-on-surface">{{ $license->user->name }}</p>
                                <p class="mt-1 text-body-sm text-vd-muted">{{ $license->user->email }}</p>
                                <p class="mt-1 text-body-sm text-vd-muted">
                                    {{ $license->client_name ?? $license->user->organization?->name ?? 'Unassigned' }}
                                </p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-label-md text-vd-on-surface">{{ $license->product->name }}</p>
                                @if ($license->subProduct)
                                    <p class="mt-1 text-body-sm text-vd-muted">{{ $license->subProduct->name }}</p>
                                @elseif ($license->is_parent_only)
                                    <span class="vd-chip mt-1">Parent only</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-label-md text-vd-on-surface">{{ $license->licenseType->name }}</td>
                            <td class="px-6 py-4 text-body-sm text-vd-muted">{{ $license->quantity }}</td>
                            <td class="px-6 py-4 text-body-sm text-vd-muted">
                                <span class="text-vd-on-surface">{{ $license->active_activations_count }}</span>
                                / {{ $license->max_activations ?? '∞' }}
                            </td>
                            <td class="px-6 py-4">
                                @if ($license->isExpired())
                                    <span class="vd-chip-error">Expired</span>
                                @elseif ($days !== null && $days <= 30)
                                    <span class="vd-chip-warning">{{ $days }}d left</span>
                                @else
                                    <span class="vd-chip-success">{{ $license->expired_date ? 'Active' : 'No expiry' }}</span>
                                @endif
                                <p class="mt-1 text-body-sm text-vd-muted">
                                    {{ $license->expired_date?->format('M j, Y') ?? 'Never' }}
                                </p>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <x-button variant="ghost" :href="route('admin.licenses.show', $license)">View</x-button>
                                    <x-button variant="ghost" :href="route('admin.licenses.edit', $license)">Edit</x-button>
                                    <form method="POST" action="{{ route('admin.licenses.destroy', $license) }}"
                                          onsubmit="return confirm('Delete this license? This will also remove related activation records.')">
                                        @csrf
                                        @method('DELETE')
                                        <x-button variant="danger">Delete</x-button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-body-sm text-vd-muted">
                                No licenses have been issued.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-vd-border px-6 py-4">
            {{ $licenses->links() }}
        </div>
    </div>
@endsection
