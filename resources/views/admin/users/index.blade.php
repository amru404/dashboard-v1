@extends('layouts.admin')

@section('title', 'Users')

@section('content')
    <x-page-header
        title="Users"
        subtitle="Admin and customer accounts with role, organization, and active status controls."
    >
        <x-slot name="actions">
            <x-button :href="route('admin.users.create')">Create User</x-button>
        </x-slot>
    </x-page-header>

    {{-- Filters --}}
    <div class="vd-card mb-6">
        <form method="GET" action="{{ route('admin.users.index') }}"
              class="grid gap-4 sm:grid-cols-2 lg:grid-cols-[1fr_1fr_0.8fr_1.2fr_auto]">
            <div>
                <x-form-label for="organization_id" value="Organization" />
                <select id="organization_id" name="organization_id" class="vd-input mt-1">
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
                <x-form-label for="role" value="Role" />
                <select id="role" name="role" class="vd-input mt-1">
                    <option value="">All roles</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role }}" @selected(($filters['role'] ?? '') === $role)>{{ ucfirst($role) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <x-form-label for="status" value="Status" />
                <select id="status" name="status" class="vd-input mt-1">
                    <option value="">All statuses</option>
                    <option value="active"   @selected(($filters['status'] ?? '') === 'active')>Active</option>
                    <option value="inactive" @selected(($filters['status'] ?? '') === 'inactive')>Inactive</option>
                </select>
            </div>
            <div>
                <x-form-label for="search" value="Search" />
                <x-form-input id="search" name="search" value="{{ $filters['search'] ?? '' }}"
                    placeholder="Name or email" class="mt-1" />
            </div>
            <div class="flex items-end gap-2">
                <x-button type="submit">Filter</x-button>
                <x-button variant="secondary" :href="route('admin.users.index')">Clear</x-button>
            </div>
        </form>
    </div>

    <div class="vd-card overflow-hidden !p-0">
        <div class="overflow-x-auto">
            <table class="vd-table">
                <thead class="bg-vd-surface">
                    <tr>
                        <th class="vd-thead">User</th>
                        <th class="vd-thead">Organization</th>
                        <th class="vd-thead">Role</th>
                        <th class="vd-thead">Status</th>
                        <th class="px-6 py-4 text-right text-eyebrow text-vd-muted tracking-widest">Actions</th>
                    </tr>
                </thead>
                <tbody class="vd-tbody">
                    @forelse ($users as $user)
                        <tr>
                            <td class="px-6 py-4">
                                <p class="text-label-md text-vd-on-surface">{{ $user->name }}</p>
                                <p class="mt-1 text-body-sm text-vd-muted">{{ $user->email }}</p>
                            </td>
                            <td class="px-6 py-4 text-body-sm text-vd-muted">{{ $user->organization?->name ?? 'Unassigned' }}</td>
                            <td class="px-6 py-4">
                                <span class="vd-chip capitalize">{{ $user->role }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <x-badge :active="$user->is_active" />
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <x-button variant="ghost" :href="route('admin.users.show', $user)">View</x-button>
                                    <x-button variant="secondary" :href="route('admin.users.edit', $user)">Edit</x-button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-body-sm text-vd-muted">
                                No users have been created.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-vd-border px-6 py-4">
            {{ $users->links() }}
        </div>
    </div>
@endsection
