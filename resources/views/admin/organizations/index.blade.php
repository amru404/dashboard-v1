@extends('layouts.admin')

@section('title', 'Organizations')

@section('content')
    <x-page-header
        title="Organizations"
        subtitle="Customer and internal organizations used to group users and future license ownership."
    >
        <x-slot name="actions">
            <x-button :href="route('admin.organizations.create')">Create organization</x-button>
        </x-slot>
    </x-page-header>

    <x-card class="overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-madani-border">
                <thead class="bg-madani-ghost">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Organization</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Contact</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Users</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-madani-border bg-white">
                    @forelse ($organizations as $organization)
                        <tr>
                            <td class="px-6 py-4">
                                <p class="font-semibold text-madani-deep">{{ $organization->name }}</p>
                                <p class="mt-1 text-sm text-madani-muted">{{ $organization->code }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-madani-muted">
                                <p>{{ $organization->email ?? 'No email' }}</p>
                                <p class="mt-1">{{ $organization->phone ?? 'No phone' }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-madani-deep">{{ $organization->users_count }}</td>
                            <td class="px-6 py-4">
                                <x-badge :active="$organization->is_active" />
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <x-button variant="ghost" :href="route('admin.organizations.show', $organization)">View</x-button>
                                    <x-button variant="secondary" :href="route('admin.organizations.edit', $organization)">Edit</x-button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-sm text-madani-muted">No organizations have been created.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-madani-border px-6 py-4">
            {{ $organizations->links() }}
        </div>
    </x-card>
@endsection
