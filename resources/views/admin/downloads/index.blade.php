@extends('layouts.admin')

@section('title', 'Download Items')

@section('content')

{{-- ── Page Header ── --}}
<div class="mb-8">
    <div class="flex items-start justify-between gap-4 mb-2">
        <div class="flex-1">
            <h1 class="text-3xl font-bold text-white mb-2">Download Items</h1>
            <p class="text-base text-gray-300">
                Private storage records connected to entitled products and customer downloads.
            </p>
        </div>
        <div class="shrink-0">
            <a href="{{ route('admin.download-items.create') }}" class="inline-flex items-center px-4 py-2.5 rounded-lg bg-vd-primary hover:bg-vd-primary/90 text-white font-semibold text-sm transition-colors">
                Register Download
            </a>
        </div>
    </div>
</div>

{{-- Warning Info Box --}}
<div class="vd-card  border-[#2a3f5f] mb-6 flex items-start gap-3">
    <svg class="mt-0.5 h-5 w-5 shrink-0 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
    </svg>
    <p class="text-sm text-gray-300 leading-relaxed">
        Files must live under <code class="rounded px-1.5 py-0.5 bg-[#0f1829] font-mono text-xs text-cyan-400 border border-[#2a3f5f]">storage/app/private/downloads</code>.
        Do not place installer files in <code class="rounded px-1.5 py-0.5 bg-[#0f1829] font-mono text-xs text-cyan-400 border border-[#2a3f5f]">public/</code> — customer delivery streams private files through a controller with entitlement checks.
    </p>
</div>

<div class="vd-card  border-[#2a3f5f] overflow-hidden !p-0">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-[#0f1829]/30 border-b border-[#2a3f5f]">
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">File</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Expiry</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Logs</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#2a3f5f]">
                @forelse ($downloadItems as $downloadItem)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium text-white">{{ $downloadItem->file_name }}</p>
                            <p class="mt-1 text-sm text-gray-400">
                                {{ $downloadItem->version ?? 'No version' }}
                                &mdash;
                                {{ number_format($downloadItem->file_size / 1048576, 2) }} MB
                            </p>
                            <p class="mt-1 font-mono text-xs text-gray-500">{{ $downloadItem->file_path }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-400">{{ $downloadItem->product->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-400">
                            {{ $downloadItem->user?->name ?? 'All entitled users' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-400">
                            {{ $downloadItem->expired_date?->format('M j, Y') ?? 'Never' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-400">{{ $downloadItem->logs_count }}</td>
                        <td class="px-6 py-4">
                            @if ($downloadItem->isExpired())
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-500/20 text-red-400 border border-red-500/30">Expired</span>
                            @elseif ($downloadItem->is_active)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-500/20 text-green-400 border border-green-500/30">Active</span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-500/20 text-gray-400 border border-gray-500/30">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex flex-wrap justify-end gap-2">
                                <a href="{{ route('admin.download-items.show', $downloadItem) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-gray-300 hover:text-white transition-colors">View</a>
                                <a href="{{ route('admin.download-items.edit', $downloadItem) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-gray-300 hover:text-white transition-colors">Edit</a>
                                <form method="POST" action="{{ route('admin.download-items.destroy', $downloadItem) }}"
                                      onsubmit="return confirm('Delete this download item? The private file will remain in storage.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-red-400 hover:text-red-300 transition-colors">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-400">
                            No download items have been registered.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="border-t border-[#2a3f5f] px-6 py-4">
        {{ $downloadItems->links() }}
    </div>
</div>
@endsection
