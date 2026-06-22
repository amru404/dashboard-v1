@extends('layouts.admin')

@section('title', 'License Types')

@section('content')
    <x-page-header title="License types" subtitle="License categories used when issuing installer-facing license records.">
        <x-slot name="actions">
            <x-button :href="route('admin.license-types.create')">Create type</x-button>
        </x-slot>
    </x-page-header>

    <x-card class="overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-madani-border">
                <thead class="bg-madani-ghost">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Type</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Licenses</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-madani-border bg-white">
                    @forelse ($licenseTypes as $licenseType)
                        <tr>
                            <td class="px-6 py-4">
                                <p class="font-semibold text-madani-deep">{{ $licenseType->name }}</p>
                                <p class="mt-1 text-sm text-madani-muted">{{ $licenseType->code ?? 'No code' }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-madani-deep">{{ $licenseType->licenses_count }}</td>
                            <td class="px-6 py-4"><x-badge :active="$licenseType->is_active" /></td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <x-button variant="ghost" :href="route('admin.license-types.show', $licenseType)">View</x-button>
                                    <x-button variant="ghost" :href="route('admin.license-types.edit', $licenseType)">Edit</x-button>
                                    <form method="POST" action="{{ route('admin.license-types.destroy', $licenseType) }}" onsubmit="return confirm('Delete this license type?')">
                                        @csrf
                                        @method('DELETE')
                                        <x-button variant="ghost">Delete</x-button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-sm text-madani-muted">No license types have been created.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-madani-border px-6 py-4">
            {{ $licenseTypes->links() }}
        </div>
    </x-card>
@endsection
