@extends('layouts.admin')

@section('title', 'Download Details')

@section('content')
    <x-page-header title="Download details" subtitle="{{ $downloadItem->file_name }}">
        <x-slot name="actions">
            <x-button variant="secondary" :href="route('admin.download-items.edit', $downloadItem)">Edit download</x-button>
            <x-button variant="secondary" :href="route('admin.download-items.index')">Back to downloads</x-button>
        </x-slot>
    </x-page-header>

    <div class="grid gap-6 lg:grid-cols-[1fr_0.7fr]">
        <x-card>
            <dl class="grid gap-5 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-semibold text-madani-muted">Product</dt>
                    <dd class="mt-1 text-base font-semibold text-madani-deep">{{ $downloadItem->product->name }}</dd>
                    <dd class="mt-1 text-sm text-madani-muted">{{ $downloadItem->product->getCatalogPath() }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-semibold text-madani-muted">Customer restriction</dt>
                    <dd class="mt-1 text-base text-madani-deep">{{ $downloadItem->user?->name ?? 'All entitled users' }}</dd>
                    @if ($downloadItem->user)
                        <dd class="mt-1 text-sm text-madani-muted">{{ $downloadItem->user->organization?->name ?? 'Unassigned' }}</dd>
                    @endif
                </div>
                <div>
                    <dt class="text-sm font-semibold text-madani-muted">Version</dt>
                    <dd class="mt-1 text-base text-madani-deep">{{ $downloadItem->version ?? 'No version' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-semibold text-madani-muted">Status</dt>
                    <dd class="mt-1"><x-badge :active="$downloadItem->is_active && ! $downloadItem->isExpired()">{{ $downloadItem->isExpired() ? 'Expired' : ($downloadItem->is_active ? 'Active' : 'Inactive') }}</x-badge></dd>
                </div>
                <div>
                    <dt class="text-sm font-semibold text-madani-muted">File size</dt>
                    <dd class="mt-1 text-base text-madani-deep">{{ number_format($downloadItem->file_size / 1048576, 2) }} MB</dd>
                    <dd class="mt-1 text-sm text-madani-muted">{{ number_format($downloadItem->file_size) }} bytes</dd>
                </div>
                <div>
                    <dt class="text-sm font-semibold text-madani-muted">Expires</dt>
                    <dd class="mt-1 text-base text-madani-deep">{{ $downloadItem->expired_date?->format('M j, Y') ?? 'Never' }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-sm font-semibold text-madani-muted">Private path</dt>
                    <dd class="mt-2 rounded-xl border border-madani-border bg-madani-ghost px-4 py-3 font-mono text-sm text-madani-deep">{{ $downloadItem->file_path }}</dd>
                </div>
            </dl>
        </x-card>

        <x-card>
            <p class="text-sm font-semibold text-madani-muted">Download logs</p>
            <p class="mt-3 text-4xl font-extrabold text-madani-deep">{{ $downloadItem->logs_count }}</p>
            <p class="mt-2 text-sm leading-6 text-madani-muted">Download logs will be written when Day 14 adds customer file streaming.</p>

            <form method="POST" action="{{ route('admin.download-items.destroy', $downloadItem) }}" class="mt-6" onsubmit="return confirm('Delete this download item?')">
                @csrf
                @method('DELETE')
                <x-button variant="danger">Delete download item</x-button>
            </form>
        </x-card>
    </div>

    <x-card class="mt-6 overflow-hidden p-0">
        <div class="border-b border-madani-border bg-madani-ghost px-6 py-5">
            <h2 class="text-lg font-bold text-madani-deep">Recent download logs</h2>
            <p class="mt-1 text-sm text-madani-muted">Audit trail for customer downloads.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-madani-border">
                <thead class="bg-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Customer</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">IP address</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Downloaded</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-madani-border bg-white">
                    @forelse ($downloadItem->logs as $log)
                        <tr>
                            <td class="px-6 py-4">
                                <p class="font-semibold text-madani-deep">{{ $log->user->name }}</p>
                                <p class="mt-1 text-sm text-madani-muted">{{ $log->user->organization?->name ?? 'Unassigned' }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-madani-muted">{{ $log->ip_address ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-madani-muted">{{ $log->downloaded_at?->format('M j, Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-10 text-center text-sm text-madani-muted">No download logs have been recorded.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
@endsection
