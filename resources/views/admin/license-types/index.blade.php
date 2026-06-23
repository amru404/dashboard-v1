@extends('layouts.admin')

@section('title', 'License Types')

@section('content')
    <x-page-header
        title="License Types"
        subtitle="License categories used when issuing installer-facing license records."
    >
        <x-slot name="actions">
            <x-button :href="route('admin.license-types.create')">Create Type</x-button>
        </x-slot>
    </x-page-header>

    <div class="vd-card overflow-hidden !p-0">
        <div class="overflow-x-auto">
            <table class="vd-table">
                <thead class="bg-vd-surface">
                    <tr>
                        <th class="vd-thead">Type</th>
                        <th class="vd-thead">Licenses</th>
                        <th class="vd-thead">Status</th>
                        <th class="px-6 py-4 text-right text-eyebrow text-vd-muted tracking-widest">Actions</th>
                    </tr>
                </thead>
                <tbody class="vd-tbody">
                    @forelse ($licenseTypes as $licenseType)
                        <tr>
                            <td class="px-6 py-4">
                                <p class="text-label-md text-vd-on-surface">{{ $licenseType->name }}</p>
                                <p class="mt-1 font-mono text-body-sm text-vd-muted">{{ $licenseType->code ?? '—' }}</p>
                            </td>
                            <td class="px-6 py-4 text-label-md text-vd-on-surface">{{ $licenseType->licenses_count }}</td>
                            <td class="px-6 py-4"><x-badge :active="$licenseType->is_active" /></td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <x-button variant="ghost" :href="route('admin.license-types.show', $licenseType)">View</x-button>
                                    <x-button variant="ghost" :href="route('admin.license-types.edit', $licenseType)">Edit</x-button>
                                    <form method="POST" action="{{ route('admin.license-types.destroy', $licenseType) }}"
                                          onsubmit="return confirm('Delete this license type?')">
                                        @csrf @method('DELETE')
                                        <x-button variant="danger">Delete</x-button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-body-sm text-vd-muted">
                                No license types have been created.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-vd-border px-6 py-4">
            {{ $licenseTypes->links() }}
        </div>
    </div>
@endsection
