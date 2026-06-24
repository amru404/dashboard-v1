@extends('layouts.admin')

@section('title', 'Users')

@section('content')

{{-- ── Page Header ── --}}
<div class="mb-8">
    <div class="flex items-start justify-between gap-4 mb-2">
        <div class="flex-1">
            <h1 class="text-3xl font-bold text-white mb-2">Users</h1>
            <p class="text-base text-gray-300">
                Admin and customer accounts with role, organization, and active status controls.
            </p>
        </div>
        <div class="shrink-0">
            <a href="{{ route('admin.users.create') }}" class="inline-flex items-center px-4 py-2.5 rounded-lg bg-vd-primary hover:bg-vd-primary/90 text-white font-semibold text-sm transition-colors">
                Create User
            </a>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="vd-card  border-[#2a3f5f] mb-6">
    <form method="GET" action="{{ route('admin.users.index') }}"
          class="grid gap-4 sm:grid-cols-2 lg:grid-cols-[1fr_1fr_0.8fr_1.2fr_auto]">
        <div>
            <label for="organization_id" class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Organization</label>
            <select id="organization_id" name="organization_id" class="w-full px-3 py-2 bg-[#0f1829] border border-[#2a3f5f] rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-vd-primary focus:border-transparent">
                <option value="">All organizations</option>
                @foreach ($organizations as $organization)
                    <option value="{{ $organization->id }}"
                        @selected((string) ($filters['organization_id'] ?? '') === (string) $organization->id)>
                        {{ $organization->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="role" class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Role</label>
            <select id="role" name="role" class="w-full px-3 py-2 bg-[#0f1829] border border-[#2a3f5f] rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-vd-primary focus:border-transparent">
                <option value="">All roles</option>
                @foreach ($roles as $role)
                    <option value="{{ $role }}" @selected(($filters['role'] ?? '') === $role)>{{ ucfirst($role) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="status" class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Status</label>
            <select id="status" name="status" class="w-full px-3 py-2 bg-[#0f1829] border border-[#2a3f5f] rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-vd-primary focus:border-transparent">
                <option value="">All statuses</option>
                <option value="active"   @selected(($filters['status'] ?? '') === 'active')>Active</option>
                <option value="inactive" @selected(($filters['status'] ?? '') === 'inactive')>Inactive</option>
            </select>
        </div>
        <div>
            <label for="search" class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Search</label>
            <input id="search" name="search" type="text" value="{{ $filters['search'] ?? '' }}"
                placeholder="Name or email" class="w-full px-3 py-2 bg-[#0f1829] border border-[#2a3f5f] rounded-lg text-white text-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-vd-primary focus:border-transparent" />
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg bg-vd-primary hover:bg-vd-primary/90 text-white font-semibold text-sm transition-colors">Filter</button>
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-white/10 hover:bg-white/15 text-white font-semibold text-sm border border-white/20 transition-colors">Clear</a>
        </div>
    </form>
</div>

<div class="vd-card  border-[#2a3f5f] overflow-hidden !p-0">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-[#0f1829]/30 border-b border-[#2a3f5f]">
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Organization</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#2a3f5f]">
                @forelse ($users as $user)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium text-white">{{ $user->name }}</p>
                            <p class="mt-1 text-sm text-gray-400">{{ $user->email }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-400">{{ $user->organization?->name ?? 'Unassigned' }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-500/20 text-blue-400 border border-blue-500/30 capitalize">{{ $user->role }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @if ($user->is_active)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-500/20 text-green-400 border border-green-500/30">Active</span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-500/20 text-gray-400 border border-gray-500/30">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.users.show', $user) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-gray-300 hover:text-white transition-colors">View</a>
                                <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-gray-300 hover:text-white transition-colors">Edit</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-400">
                            No users have been created.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="border-t border-[#2a3f5f] px-6 py-4">
        {{ $users->links() }}
    </div>
</div>
@endsection
