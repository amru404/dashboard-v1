@extends('layouts.admin')

@section('title', 'Download Items')

@section('content')
    <x-page-header
        title="Download Items"
        subtitle="Private storage records connected to entitled products and customer downloads."
    >
        <x-slot name="actions">
            <x-button :href="route('admin.download-items.create')">Register Download</x-button>
        </x-slot>
    </x-page-header>

    <div class="vd-card mb-6 flex items-start gap-3">
        <svg class="mt-0.5 h-4 w-4 shrink-0 text-vd-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
        </svg>
        <p class="text-body-sm text-vd-muted leading-relaxed">
            Files must live under <code class="rounded px-1 bg-white/10 font-mono text-[11px]">storage/app/private/downloads</code>.
            Do not place installer files in <code class="rounded px-1 bg-white/10 font-mono text-[11px]">public/</code> — customer delivery streams private files through a controller with entitlement checks.
        </p>
    </div>

    <div class="vd-card overflow-hidden !p-0">
        <div class="overflow-x-auto">
            <table class="vd-table">
                <thead class="bg-vd-surface">
                    <tr>
                        <th class="vd-thead">File</th>
                        <th class="vd-thead">Product</th>
                        <th class="vd-thead">Customer</th>
                        <th class="vd-thead">Expiry</th>
                        <th class="vd-thead">Logs</th>
                        <th class="vd-thead">Status</th>
                        <th class="px-6 py-4 text-right text-eyebrow text-vd-muted tracking-widest">Actions</th>
                    </tr>
                </thead>
                <tbody class="vd-tbody">
                    @forelse ($downloadItems as $downloadItem)
                        <tr>
                            <td class="px-6 py-4">
                                <p class="text-label-md text-vd-on-surface">{{ $downloadItem->file_name }}</p>
                                <p class="mt-1 text-body-sm text-vd-muted">
                                    {{ $downloadItem->version ?? 'No version' }}
                                    &mdash;
                                    {{ number_format($downloadItem->file_size / 1048576, 2) }} MB
                                </p>
                                <p class="mt-1 font-mono text-[11px] text-vd-muted">{{ $downloadItem->file_path }}</p>
                            </td>
                            <td class="px-6 py-4 text-body-sm text-vd-muted">{{ $downloadItem->product->name }}</td>
                            <td class="px-6 py-4 text-body-sm text-vd-muted">
                                {{ $downloadItem->user?->name ?? 'All entitled users' }}
                            </td>
                            <td class="px-6 py-4 text-body-sm text-vd-muted">
                                {{ $downloadItem->expired_date?->format('M j, Y') ?? 'Never' }}
                            </td>
                            <td class="px-6 py-4 text-body-sm text-vd-muted">{{ $downloadItem->logs_count }}</td>
                            <td class="px-6 py-4">
                                <x-badge :active="$downloadItem->is_active && ! $downloadItem->isExpired()">
                                    {{ $downloadItem->isExpired() ? 'Expired' : ($downloadItem->is_active ? 'Active' : 'Inactive') }}
                                </x-badge>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <x-button variant="ghost" :href="route('admin.download-items.show', $downloadItem)">View</x-button>
                                    <x-button variant="ghost" :href="route('admin.download-items.edit', $downloadItem)">Edit</x-button>
                                    <form method="POST" action="{{ route('admin.download-items.destroy', $downloadItem) }}"
                                          onsubmit="return confirm('Delete this download item? The private file will remain in storage.')">
                                        @csrf @method('DELETE')
                                        <x-button variant="danger">Delete</x-button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-body-sm text-vd-muted">
                                No download items have been registered.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-vd-border px-6 py-4">
            {{ $downloadItems->links() }}
        </div>
    </div>
@endsection
