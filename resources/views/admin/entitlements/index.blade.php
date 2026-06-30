@extends('layouts.admin')

@section('title', 'Entitlements')

@section('content')

{{-- ── Page Header ── --}}
<div class="mb-8">
    <div class="flex items-start justify-between gap-4 mb-2">
        <div class="flex-1">
            <h1 class="text-3xl font-bold text-white mb-2">Entitlements</h1>
            <p class="text-base text-gray-300">
                Access grants that control product visibility and download availability — separate from installer licenses.
            </p>
        </div>
        <div class="shrink-0">
            <x-button :href="route('admin.entitlements.create')" variant="primary">
                New Entitlement
            </x-button>
        </div>
    </div>
</div>


{{-- Search & Filters --}}
<div class="vd-card border-[#2a3f5f] mb-6">
    <form method="GET" action="{{ route('admin.entitlements.index') }}" class="flex flex-col md:flex-row gap-4">
        <div class="flex-1">
            <input 
                type="text" 
                name="search" 
                value="{{ request('search') }}" 
                placeholder="Search by customer, organization, or product..." 
                class="w-full rounded-xl border border-[#2a3f5f] bg-[#0f1829] px-4 py-2.5 text-sm text-white placeholder-gray-500 outline-none transition focus:border-vd-primary focus:ring-2 focus:ring-vd-primary/20"
            />
        </div>
        <div class="w-full md:w-48">
            <select 
                name="status" 
                class="w-full rounded-xl border border-[#2a3f5f] bg-[#0f1829] px-4 py-2.5 text-sm text-white outline-none transition focus:border-vd-primary focus:ring-2 focus:ring-vd-primary/20"
            >
                <option value="">All Status</option>
                <option value="active" @selected(request('status') === 'active')>Active</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
            </select>
        </div>
        <div class="flex gap-2">
            <x-button type="submit" variant="primary">
                Search
            </x-button>
            @if(request('search') || request('status'))
                <x-button :href="route('admin.entitlements.index')" variant="secondary">
                    Clear
                </x-button>
            @endif
        </div>
    </form>
</div>

<div class="vd-card  border-[#2a3f5f] overflow-hidden !p-0">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-[#0f1829]/30 border-b border-[#2a3f5f]">
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Access Window</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Download Window</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#2a3f5f]">
                    @forelse ($entitlements as $entitlement)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-white">{{ $entitlement->user->name }}</p>
                                <p class="mt-1 text-sm text-gray-400">
                                    {{ $entitlement->user->organization?->name ?? 'Unassigned' }}
                                </p>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-white">{{ $entitlement->product->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-400">
                                {{ $entitlement->start_date->format('M j, Y') }}
                                &rarr;
                                {{ $entitlement->end_date?->format('M j, Y') ?? 'open ended' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-400">
                                {{ $entitlement->download_expired_date?->format('M j, Y') ?? 'No separate limit' }}
                            </td>
                            <td class="px-6 py-4">
                                @if ($entitlement->status === 'active')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-500/20 text-green-400 border border-green-500/30">{{ ucfirst($entitlement->status) }}</span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-500/20 text-gray-400 border border-gray-500/30">{{ ucfirst($entitlement->status) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <a href="{{ route('admin.entitlements.show', $entitlement) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-gray-300 hover:text-white transition-colors">View</a>
                                    <a href="{{ route('admin.entitlements.edit', $entitlement) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-gray-300 hover:text-white transition-colors">Edit</a>
                                    <form method="POST" action="{{ route('admin.entitlements.destroy', $entitlement) }}"
                                          onsubmit="return confirm('Delete this entitlement?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-red-400 hover:text-red-300 transition-colors">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-400">
                                No entitlements have been granted.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-[#2a3f5f] px-6 py-4">
            {{ $entitlements->links() }}
        </div>
    </div>
@endsection
