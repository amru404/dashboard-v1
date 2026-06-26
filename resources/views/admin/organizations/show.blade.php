@extends('layouts.admin')

@section('title', $organization->name)

@section('content')
    {{-- ── Page Header ── --}}
    <div class="mb-8">
        <div class="flex items-start justify-between gap-4">
            <div class="flex-1">
                <h1 class="text-3xl font-bold text-white mb-2">{{ $organization->name }}</h1>
                <p class="text-base text-gray-300">
                    Organization profile and account assignment status.
                </p>
            </div>
            <div class="shrink-0">
                <a href="{{ route('admin.organizations.edit', $organization) }}" class="inline-flex items-center px-4 py-2.5 rounded-lg text-gray-300 hover:text-white font-semibold text-sm transition-colors">
                    Edit organization
                </a>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-[1fr_0.7fr]">
        {{-- ── Main Info Card ── --}}
        <div class="vd-card border-[#2a3f5f]">
            <dl class="grid gap-5 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-semibold text-gray-400">Code</dt>
                    <dd class="mt-1 text-base font-bold text-white">{{ $organization->code }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-semibold text-gray-400">Status</dt>
                    <dd class="mt-1">
                        @if ($organization->is_active)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-500/20 text-green-400 border border-green-500/30">Active</span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-500/20 text-gray-400 border border-gray-500/30">Inactive</span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-semibold text-gray-400">Email</dt>
                    <dd class="mt-1 text-base text-gray-300">{{ $organization->email ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-semibold text-gray-400">Phone</dt>
                    <dd class="mt-1 text-base text-gray-300">{{ $organization->phone ?? '-' }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-sm font-semibold text-gray-400">Address</dt>
                    <dd class="mt-1 whitespace-pre-line text-base leading-7 text-gray-300">{{ $organization->address ?? '-' }}</dd>
                </div>
            </dl>
        </div>

        {{-- ── Account Controls Card ── --}}
        <div class="vd-card border-[#2a3f5f]">
            <p class="text-sm font-semibold text-white">Assigned users</p>
            <p class="mt-3 text-4xl font-extrabold text-white">{{ $organization->users_count }}</p>
            <p class="mt-2 text-sm leading-6 text-gray-400">Organizations with assigned users are protected from deletion.</p>

            <form method="POST" action="{{ route('admin.organizations.destroy', $organization) }}" class="mt-6" onsubmit="return confirm('Delete this organization?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center px-4 py-2.5 rounded-lg bg-red-500/10 hover:bg-red-500/20 text-red-400 hover:text-red-300 font-semibold text-sm transition-colors">
                    Delete organization
                </button>
            </form>
        </div>
    </div>

    {{-- ── Related Users Table ── --}}
    <div class="mt-6 vd-card border-[#2a3f5f] overflow-hidden !p-0">
        <div class="flex flex-col gap-3 border-b border-[#2a3f5f] bg-[#0f1829]/30 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-bold text-white">Related users</h2>
                <p class="mt-1 text-sm text-gray-400">Accounts currently assigned to this organization.</p>
            </div>
            <a href="{{ route('admin.users.create') }}" class="inline-flex items-center px-4 py-2.5 rounded-lg text-gray-300 hover:text-white font-semibold text-sm transition-colors">
                New user
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-[#0f1829]/30 border-b border-[#2a3f5f]">
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#2a3f5f]">
                    @forelse ($organization->users as $user)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4">
                                <p class="font-semibold text-white">{{ $user->name }}</p>
                                <p class="mt-1 text-sm text-gray-400">{{ $user->email }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold capitalize text-white">{{ $user->role }}</td>
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
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-400">
                                No users are assigned to this organization.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
