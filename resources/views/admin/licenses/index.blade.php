@extends('layouts.admin')

@section('title', 'Licenses')

@section('content')

{{-- ── Page Header ── --}}
<div class="mb-8">
    <div class="flex items-start justify-between gap-4 mb-2">
        <div class="flex-1">
            <h1 class="text-3xl font-bold text-white mb-2">Licenses</h1>
            <p class="text-base text-gray-300">
                Installer-facing license records assigned to customer users. Parent licenses act as containers; actual keys live at the sub-product level.
            </p>
        </div>
        <div class="flex gap-2 shrink-0">
            {{-- <a href="{{ route('admin.licenses.batch-create') }}" class="inline-flex items-center px-4 py-2.5 rounded-lg bg-white/10 hover:bg-white/15 text-white font-semibold text-sm border border-white/20 transition-colors">
                Batch Issue
            </a> --}}
            <a href="{{ route('admin.licenses.create') }}" class="inline-flex items-center px-4 py-2.5 rounded-lg bg-vd-primary hover:bg-vd-primary/90 text-white font-semibold text-sm transition-colors">
                New License
            </a>
        </div>
    </div>
</div>

<div class="vd-card  border-[#2a3f5f] overflow-hidden !p-0">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-[#0f1829]/30 border-b border-[#2a3f5f]">
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Key</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Qty</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Activations</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Expiry</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#2a3f5f]">
                    @forelse ($licenses as $license)
                        @php $days = $license->daysUntilExpiry(); @endphp
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4 font-mono text-sm text-white">
                                {{ $license->masked_license_key }}
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-white">{{ $license->user->name }}</p>
                                <p class="mt-1 text-sm text-gray-400">{{ $license->user->email }}</p>
                                <p class="mt-1 text-sm text-gray-400">
                                    {{ $license->client_name ?? $license->user->organization?->name ?? 'Unassigned' }}
                                </p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-white">{{ $license->product->name }}</p>
                                @if ($license->subProduct)
                                    <p class="mt-1 text-sm text-gray-400">{{ $license->subProduct->name }}</p>
                                @elseif ($license->is_parent_only)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-[#0f1829] text-gray-300 border border-[#2a3f5f] mt-1">Parent only</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-white">{{ $license->licenseType->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-400">{{ $license->quantity }}</td>
                            <td class="px-6 py-4 text-sm text-gray-400">
                                <span class="text-white font-medium">{{ $license->active_activations_count }}</span>
                                / {{ $license->max_activations ?? '∞' }}
                            </td>
                            <td class="px-6 py-4">
                                @if ($license->isExpired())
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-500/20 text-red-400 border border-red-500/30">Expired</span>
                                @elseif ($days !== null && $days <= 30)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-orange-500/20 text-orange-400 border border-orange-500/30">{{ $days }}d left</span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-500/20 text-green-400 border border-green-500/30">{{ $license->expired_date ? 'Active' : 'No expiry' }}</span>
                                @endif
                                <p class="mt-1 text-sm text-gray-400">
                                    {{ $license->expired_date?->format('M j, Y') ?? 'Never' }}
                                </p>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <a href="{{ route('admin.licenses.show', $license) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-gray-300 hover:text-white transition-colors">View</a>
                                    <a href="{{ route('admin.licenses.edit', $license) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-gray-300 hover:text-white transition-colors">Edit</a>
                                    <form method="POST" action="{{ route('admin.licenses.destroy', $license) }}"
                                          onsubmit="return confirm('Delete this license? This will also remove related activation records.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-red-400 hover:text-red-300 transition-colors">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-sm text-gray-400">
                                No licenses have been issued.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-[#2a3f5f] px-6 py-4">
            {{ $licenses->links() }}
        </div>
    </div>
@endsection
