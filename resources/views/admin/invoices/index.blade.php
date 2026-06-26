@extends('layouts.admin')

@section('title', 'Invoices')

@section('content')

{{-- ── Page Header ── --}}
<div class="mb-8">
    <div class="flex items-start justify-between gap-4 mb-2">
        <div class="flex-1">
            <h1 class="text-3xl font-bold text-white mb-2">Invoices</h1>
            <p class="text-base text-gray-300">
                Manage and distribute invoices to users.
            </p>
        </div>
        <div class="shrink-0">
            <a href="{{ route('admin.invoices.create') }}" class="inline-flex items-center px-4 py-2.5 rounded-lg bg-vd-primary hover:bg-vd-primary/90 text-white font-semibold text-sm transition-colors">
                Create Invoice
            </a>
        </div>
    </div>
</div>

{{-- ── Invoices Table ── --}}
<div class="vd-card border-[#2a3f5f] !p-6">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-[#2a3f5f]">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Display Name</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Assigned Users</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">File</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Download Expires</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-400 uppercase tracking-wider">Active</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#2a3f5f]">
                @forelse ($invoices as $invoice)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-4 py-3 text-white font-medium">{{ $invoice->display_name }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-vd-primary/10 text-vd-primary border border-vd-primary/20">
                                    {{ $invoice->users->count() }}
                                </span>
                                @if ($invoice->users->isNotEmpty())
                                    <div class="flex -space-x-2">
                                        @foreach ($invoice->users->take(3) as $user)
                                            <div class="w-6 h-6 rounded-full bg-vd-primary/20 border border-vd-primary/30 flex items-center justify-center text-xs font-semibold text-vd-primary" title="{{ $user->name }}">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                        @endforeach
                                        @if ($invoice->users->count() > 3)
                                            <div class="w-6 h-6 rounded-full bg-gray-500/20 border border-gray-500/30 flex items-center justify-center text-xs font-semibold text-gray-400">
                                                +{{ $invoice->users->count() - 3 }}
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            @switch($invoice->status)
                                @case('draft')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-500/20 text-gray-400 border border-gray-500/30">Draft</span>
                                @break
                                @case('sent')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-500/20 text-blue-400 border border-blue-500/30">Sent</span>
                                @break
                                @case('paid')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-500/20 text-green-400 border border-green-500/30">Paid</span>
                                @break
                                @case('cancelled')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-500/20 text-red-400 border border-red-500/30">Cancelled</span>
                                @break
                            @endswitch
                        </td>
                        <td class="px-4 py-3 text-gray-400">
                            @if ($invoice->file_path)
                                <a href="{{ route('admin.invoices.download', $invoice) }}" class="text-vd-primary hover:underline text-sm">
                                    {{ $invoice->original_filename }}
                                    <span class="text-xs text-gray-500">({{ number_format($invoice->file_size / 1024 / 1024, 1) }}MB)</span>
                                </a>
                            @else
                                <span class="text-gray-500">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-400 text-sm">
                            {{ $invoice->download_expired_at?->format('M j, Y') ?? 'No limit' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if ($invoice->is_active)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-500/20 text-green-400 border border-green-500/30">Yes</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-500/20 text-gray-400 border border-gray-500/30">No</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('admin.invoices.edit', $invoice) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-vd-primary/20 hover:bg-vd-primary/30 text-vd-primary font-semibold text-xs border border-vd-primary/30 transition-colors">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('admin.invoices.destroy', $invoice) }}" onsubmit="return confirm('Delete this invoice?')" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-red-500/20 hover:bg-red-500/30 text-red-400 font-semibold text-xs border border-red-500/30 transition-colors">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-gray-400">
                            No invoices found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if ($invoices->hasPages())
        <div class="mt-6">
            {{ $invoices->links() }}
        </div>
    @endif
</div>

@endsection
