@extends('layouts.admin')

@section('title', $organization->name)

@section('content')
    <x-page-header title="{{ $organization->name }}" subtitle="Organization profile and account assignment status.">
        <x-slot name="actions">
            <x-button variant="secondary" :href="route('admin.organizations.edit', $organization)">Edit organization</x-button>
        </x-slot>
    </x-page-header>

    <div class="grid gap-6 lg:grid-cols-[1fr_0.7fr]">
        <x-card>
            <dl class="grid gap-5 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-semibold text-madani-muted">Code</dt>
                    <dd class="mt-1 text-base font-bold text-madani-deep">{{ $organization->code }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-semibold text-madani-muted">Status</dt>
                    <dd class="mt-1"><x-badge :active="$organization->is_active" /></dd>
                </div>
                <div>
                    <dt class="text-sm font-semibold text-madani-muted">Email</dt>
                    <dd class="mt-1 text-base text-madani-deep">{{ $organization->email ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-semibold text-madani-muted">Phone</dt>
                    <dd class="mt-1 text-base text-madani-deep">{{ $organization->phone ?? '-' }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-sm font-semibold text-madani-muted">Address</dt>
                    <dd class="mt-1 whitespace-pre-line text-base leading-7 text-madani-deep">{{ $organization->address ?? '-' }}</dd>
                </div>
            </dl>
        </x-card>

        <x-card>
            <p class="text-sm font-semibold text-madani-muted">Assigned users</p>
            <p class="mt-3 text-4xl font-extrabold text-madani-deep">{{ $organization->users_count }}</p>
            <p class="mt-2 text-sm leading-6 text-madani-muted">Organizations with assigned users are protected from deletion.</p>

            <form method="POST" action="{{ route('admin.organizations.destroy', $organization) }}" class="mt-6" onsubmit="return confirm('Delete this organization?')">
                @csrf
                @method('DELETE')
                <x-button variant="danger">Delete organization</x-button>
            </form>
        </x-card>
    </div>

    <x-card class="mt-6 overflow-hidden p-0">
        <div class="flex flex-col gap-3 border-b border-madani-border bg-madani-ghost px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-bold text-madani-deep">Related users</h2>
                <p class="mt-1 text-sm text-madani-muted">Accounts currently assigned to this organization.</p>
            </div>
            <x-button variant="secondary" :href="route('admin.users.create')">Create user</x-button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-madani-border">
                <thead class="bg-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">User</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Role</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-madani-border bg-white">
                    @forelse ($organization->users as $user)
                        <tr>
                            <td class="px-6 py-4">
                                <p class="font-semibold text-madani-deep">{{ $user->name }}</p>
                                <p class="mt-1 text-sm text-madani-muted">{{ $user->email }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold capitalize text-madani-deep">{{ $user->role }}</td>
                            <td class="px-6 py-4">
                                <x-badge :active="$user->is_active" />
                            </td>
                            <td class="px-6 py-4 text-right">
                                <x-button variant="ghost" :href="route('admin.users.show', $user)">View</x-button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-sm text-madani-muted">
                                No users are assigned to this organization.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
@endsection
