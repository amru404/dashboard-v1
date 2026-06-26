@extends('layouts.user')

@section('title', 'Invoices')

@section('content')

{{-- ── Page Header ── --}}
<div class="mb-8">
    <h1 class="text-3xl font-bold text-white mb-2">Invoices</h1>
    <p class="text-base text-gray-300">
        Download your invoices and billing documents.
    </p>
</div>

{{-- ── Invoices Table ── --}}
<div class="vd-card border-[#2a3f5f] !p-0 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-[#2a3f5f] bg-[#0f1829]">
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Invoice Name</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">File</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Download Expires</th>
                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-400 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#2a3f5f]">
                @forelse ($invoices as $invoice)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4 text-white font-medium">{{ $invoice->display_name }}</td>
                        <td class="px-6 py-4">
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
                        <td class="px-6 py-4 text-gray-400">
                            @if ($invoice->file_path)
                                <div class="text-sm">
                                    <p class="text-white font-medium">{{ $invoice->original_filename }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ number_format($invoice->file_size / 1024 / 1024, 1) }}MB</p>
                                </div>
                            @else
                                <span class="text-gray-500">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-400 text-sm">
                            @if ($invoice->download_expired_at)
                                @if ($invoice->download_expired_at->isPast())
                                    <span class="text-red-400 font-medium">Expired</span>
                                    <p class="text-xs text-gray-500 mt-1">{{ $invoice->download_expired_at->format('M j, Y') }}</p>
                                @else
                                    <p>{{ $invoice->download_expired_at->format('M j, Y') }}</p>
                                @endif
                            @else
                                <p>No limit</p>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if ($invoice->file_path)
                                @if ($invoice->download_expired_at && $invoice->download_expired_at->isPast())
                                    <button disabled class="inline-flex items-center px-3 py-1.5 rounded-lg bg-gray-500/20 text-gray-400 font-semibold text-xs border border-gray-500/30 cursor-not-allowed">
                                        Download
                                    </button>
                                @else
                                    <a href="{{ route('user.invoices.download', $invoice) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-vd-primary/20 hover:bg-vd-primary/30 text-vd-primary font-semibold text-xs border border-vd-primary/30 transition-colors">
                                        Download
                                    </a>
                                @endif
                            @else
                                <span class="text-gray-500 text-xs">N/A</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-sm font-medium text-white">No invoices available</p>
                                <p class="text-xs text-gray-400 mt-1">Check back later for new invoices</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if ($invoices->hasPages())
        <div class="px-6 py-4 border-t border-[#2a3f5f]">
            {{ $invoices->links() }}
        </div>
    @endif
</div>

@endsection
