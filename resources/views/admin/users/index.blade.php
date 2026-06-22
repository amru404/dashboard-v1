@extends('layouts.admin')

@section('title', 'Users')

@section('content')
    <x-page-header
        title="Users"
        subtitle="Admin and customer accounts with role, organization, and active status controls."
    >
        <x-slot name="actions">
            <x-button :href="route('admin.users.create')">Create user</x-button>
        </x-slot>
    </x-page-header>

    <x-card class="mb-6">
        <form method="GET" action="{{ route('admin.users.index') }}" class="grid gap-4 lg:grid-cols-[1fr_1fr_0.8fr_1.2fr_auto]">
            <div>
                <x-form-label for="organization_id" value="Organization" />
                <select id="organization_id" name="organization_id" class="madani-input mt-2">
                    <option value="">All organizations</option>
                    @foreach ($organizations as $organization)
                        <option value="{{ $organization->id }}" @selected((string) ($filters['organization_id'] ?? '') === (string) $organization->id)>
                            {{ $organization->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <x-form-label for="role" value="Role" />
                <select id="role" name="role" class="madani-input mt-2">
                    <option value="">All roles</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role }}" @selected(($filters['role'] ?? '') === $role)>{{ ucfirst($role) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <x-form-label for="status" value="Status" />
                <select id="status" name="status" class="madani-input mt-2">
                    <option value="">All statuses</option>
                    <option value="active" @selected(($filters['status'] ?? '') === 'active')>Active</option>
                    <option value="inactive" @selected(($filters['status'] ?? '') === 'inactive')>Inactive</option>
                </select>
            </div>

            <div>
                <x-form-label for="search" value="Search" />
                <x-form-input id="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Name or email" class="mt-2" />
            </div>

            <div class="flex items-end gap-2">
                <x-button>Filter</x-button>
                <x-button variant="secondary" :href="route('admin.users.index')">Clear</x-button>
            </div>
        </form>
    </x-card>

    <x-card class="overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-madani-border">
                <thead class="bg-madani-ghost">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">User</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Organization</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Role</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-madani-border bg-white">
                    @forelse ($users as $user)
                        <tr>
                            <td class="px-6 py-4">
                                <p class="font-semibold text-madani-deep">{{ $user->name }}</p>
                                <p class="mt-1 text-sm text-madani-muted">{{ $user->email }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-madani-muted">{{ $user->organization?->name ?? 'Unassigned' }}</td>
                            <td class="px-6 py-4">
                                <span class="rounded-full bg-madani-ghost px-3 py-1 text-xs font-semibold capitalize text-madani-deep">{{ $user->role }}</span>
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
                            <td colspan="5" class="px-6 py-10 text-center text-sm text-madani-muted">No users have been created.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-madani-border px-6 py-4">
            {{ $users->links() }}
        </div>
    </x-card>
@endsection
