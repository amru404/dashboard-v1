@extends('layouts.admin')

@section('title', 'Download Items')

@section('content')
    <x-page-header title="Download items" subtitle="Private storage records connected to entitled products and customer downloads.">
        <x-slot name="actions">
            <x-button :href="route('admin.download-items.create')">Register download</x-button>
        </x-slot>
    </x-page-header>

    <x-card class="mb-6">
        <p class="text-sm leading-6 text-madani-muted">
            Files must live under storage/app/private/downloads. Do not place installer files in `public/`; customer delivery will stream private files through a controller.
        </p>
    </x-card>

    <x-card class="overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-madani-border">
                <thead class="bg-madani-ghost">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">File</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Product</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Customer</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Expiry</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Logs</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-madani-border bg-white">
                    @forelse ($downloadItems as $downloadItem)
                        <tr>
                            <td class="px-6 py-4">
                                <p class="font-semibold text-madani-deep">{{ $downloadItem->file_name }}</p>
                                <p class="mt-1 text-sm text-madani-muted">{{ $downloadItem->version ?? 'No version' }} - {{ number_format($downloadItem->file_size / 1048576, 2) }} MB</p>
                                <p class="mt-1 font-mono text-xs text-madani-muted">{{ $downloadItem->file_path }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-madani-muted">{{ $downloadItem->product->name }}</td>
                            <td class="px-6 py-4 text-sm text-madani-muted">{{ $downloadItem->user?->name ?? 'All entitled users' }}</td>
                            <td class="px-6 py-4 text-sm text-madani-muted">{{ $downloadItem->expired_date?->format('M j, Y') ?? 'Never' }}</td>
                            <td class="px-6 py-4 text-sm text-madani-muted">{{ $downloadItem->logs_count }}</td>
                            <td class="px-6 py-4"><x-badge :active="$downloadItem->is_active && ! $downloadItem->isExpired()">{{ $downloadItem->isExpired() ? 'Expired' : ($downloadItem->is_active ? 'Active' : 'Inactive') }}</x-badge></td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <x-button variant="ghost" :href="route('admin.download-items.show', $downloadItem)">View</x-button>
                                    <x-button variant="ghost" :href="route('admin.download-items.edit', $downloadItem)">Edit</x-button>
                                    <form method="POST" action="{{ route('admin.download-items.destroy', $downloadItem) }}" onsubmit="return confirm('Delete this download item? The private file will remain in storage.')">
                                        @csrf
                                        @method('DELETE')
                                        <x-button variant="ghost">Delete</x-button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-sm text-madani-muted">No download items have been registered.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-madani-border px-6 py-4">
            {{ $downloadItems->links() }}
        </div>
    </x-card>
@endsection
