@extends('layouts.admin')

@section('title', 'Organizations')

@section('content')

{{-- ── Page Header ── --}}
<div class="mb-8">
    <div class="flex items-start justify-between gap-4 mb-2">
        <div class="flex-1">
            <h1 class="text-3xl font-bold text-white mb-2">Organizations</h1>
            <p class="text-base text-gray-300">
                Customer and internal organizations used to group users and future license ownership.
            </p>
        </div>
        <div class="shrink-0">
            <a href="{{ route('admin.organizations.create') }}" class="inline-flex items-center px-4 py-2.5 rounded-lg bg-vd-primary hover:bg-vd-primary/90 text-white font-semibold text-sm transition-colors">
                Create Organization
            </a>
        </div>
    </div>
</div>

<div class="vd-card  border-[#2a3f5f] overflow-hidden !p-0">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-[#0f1829]/30 border-b border-[#2a3f5f]">
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Organization</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Contact</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Users</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#2a3f5f]">
                @forelse ($organizations as $organization)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium text-white">{{ $organization->name }}</p>
                            <p class="mt-1 text-sm text-gray-400 font-mono">{{ $organization->code }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-400">
                            <p>{{ $organization->email ?? '—' }}</p>
                            <p class="mt-1">{{ $organization->phone ?? '—' }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-white">{{ $organization->users_count }}</td>
                        <td class="px-6 py-4">
                            @if ($organization->is_active)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-500/20 text-green-400 border border-green-500/30">Active</span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-500/20 text-gray-400 border border-gray-500/30">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.organizations.show', $organization) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-gray-300 hover:text-white transition-colors">View</a>
                                <a href="{{ route('admin.organizations.edit', $organization) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-gray-300 hover:text-white transition-colors">Edit</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-400">
                            No organizations have been created.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="border-t border-[#2a3f5f] px-6 py-4">
        {{ $organizations->links() }}
    </div>
</div>
@endsection
