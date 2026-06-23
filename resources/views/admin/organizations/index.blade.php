@extends('layouts.admin')

@section('title', 'Organizations')

@section('content')
    <x-page-header
        title="Organizations"
        subtitle="Customer and internal organizations used to group users and future license ownership."
    >
        <x-slot name="actions">
            <x-button :href="route('admin.organizations.create')">Create Organization</x-button>
        </x-slot>
    </x-page-header>

    <div class="vd-card overflow-hidden !p-0">
        <div class="overflow-x-auto">
            <table class="vd-table">
                <thead class="bg-vd-surface">
                    <tr>
                        <th class="vd-thead">Organization</th>
                        <th class="vd-thead">Contact</th>
                        <th class="vd-thead">Users</th>
                        <th class="vd-thead">Status</th>
                        <th class="px-6 py-4 text-right text-eyebrow text-vd-muted tracking-widest">Actions</th>
                    </tr>
                </thead>
                <tbody class="vd-tbody">
                    @forelse ($organizations as $organization)
                        <tr>
                            <td class="px-6 py-4">
                                <p class="text-label-md text-vd-on-surface">{{ $organization->name }}</p>
                                <p class="mt-1 text-body-sm text-vd-muted font-mono">{{ $organization->code }}</p>
                            </td>
                            <td class="px-6 py-4 text-body-sm text-vd-muted">
                                <p>{{ $organization->email ?? '—' }}</p>
                                <p class="mt-1">{{ $organization->phone ?? '—' }}</p>
                            </td>
                            <td class="px-6 py-4 text-label-md text-vd-on-surface">{{ $organization->users_count }}</td>
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
                            <td colspan="5" class="px-6 py-12 text-center text-body-sm text-vd-muted">
                                No organizations have been created.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-vd-border px-6 py-4">
            {{ $organizations->links() }}
        </div>
    </div>
@endsection
