@extends('layouts.user')

@section('title', 'My Licenses')

@section('content')
    <x-page-header
        title="My Licenses"
        subtitle="License records assigned to your account. Parent licenses group your sub-product keys."
    />

    <div class="vd-card overflow-hidden !p-0">
        <div class="overflow-x-auto">
            <table class="vd-table">
                <thead class="bg-vd-surface">
                    <tr>
                        <th class="vd-thead">Key</th>
                        <th class="vd-thead">Product</th>
                        <th class="vd-thead">Type</th>
                        <th class="vd-thead">Activations</th>
                        <th class="vd-thead">Expiry</th>
                        <th class="px-6 py-4 text-right text-eyebrow text-vd-muted tracking-widest">Action</th>
                    </tr>
                </thead>
                <tbody class="vd-tbody">
                    @forelse ($licenses as $license)
                        <tr>
                            <td class="px-6 py-4 font-mono text-body-sm text-vd-on-surface">
                                {{ $license->masked_license_key }}
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-label-md text-vd-on-surface">{{ $license->product->name }}</p>
                                @if ($license->subProduct)
                                    <p class="mt-1 text-body-sm text-vd-muted">{{ $license->subProduct->name }}</p>
                                @elseif ($license->is_parent_only)
                                    <span class="vd-chip mt-1">Parent</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-body-sm text-vd-muted">{{ $license->licenseType->name }}</td>
                            <td class="px-6 py-4 text-body-sm text-vd-muted">
                                <span class="text-vd-on-surface">{{ $license->active_activations_count }}</span>
                                / {{ $license->max_activations ?? '∞' }}
                            </td>
                            <td class="px-6 py-4">
                                @if ($license->isExpired())
                                    <span class="vd-chip-error">Expired</span>
                                @elseif ($license->daysUntilExpiry() !== null && $license->daysUntilExpiry() <= 30)
                                    <span class="vd-chip-warning">{{ $license->daysUntilExpiry() }}d left</span>
                                @else
                                    <span class="text-body-sm text-vd-muted">
                                        {{ $license->expired_date?->format('M j, Y') ?? 'Never' }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if (! $license->is_parent_only)
                                    <a href="{{ route('user.licenses.show', $license) }}" class="vd-btn-ghost">View</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-body-sm text-vd-muted">
                                No licenses are assigned to your account.
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
