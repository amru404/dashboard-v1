@extends('layouts.admin')

@section('title', 'Invoice: ' . $invoice->display_name)

@section('content')

{{-- ── Page Header ── --}}
<div class="mb-8">
    <div class="flex items-start justify-between gap-4">
        <div class="flex-1">
            <div class="flex items-center gap-3 mb-2">
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">{{ $invoice->display_name }}</h1>
            <div class="flex items-center gap-3 mt-4">
                @switch($invoice->status)
                    @case('draft')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-gray-500/20 text-gray-400 border border-gray-500/30">Draft</span>
                    @break
                    @case('sent')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-blue-500/20 text-blue-400 border border-blue-500/30">Sent</span>
                    @break
                    @case('paid')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-500/20 text-green-400 border border-green-500/30">Paid</span>
                    @break
                    @case('cancelled')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-red-500/20 text-red-400 border border-red-500/30">Cancelled</span>
                    @break
                @endswitch
            </div>
        </div>
        <div class="flex gap-2 shrink-0">
           <a href="{{ route('admin.invoices.index') }}" class="inline-flex items-center px-4 py-2.5 rounded-lg bg-white/10 hover:bg-white/15 text-white font-semibold text-sm border border-white/20 transition-colors">
                    ← Back
                </a>
        </div>
    </div>
</div>

<div class="grid gap-6 lg:grid-cols-3">
    {{-- Main Content --}}
    <div class="lg:col-span-2 space-y-6">
        {{-- File Information --}}
        <div class="vd-card border-[#2a3f5f] !p-6">
            <h2 class="text-xl font-bold text-white mb-6">File Information</h2>
            <div class="space-y-4">
                @if ($invoice->file_path)
                    <div class="flex items-start gap-4 p-4 bg-[#0f1829]/50 rounded-lg border border-[#2a3f5f]">
                        <svg class="h-8 w-8 shrink-0 text-vd-primary mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-white truncate">{{ $invoice->original_filename }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $invoice->mime_type }}</p>
                            <p class="text-sm text-gray-400 mt-2">
                                Size: <span class="font-semibold">{{ number_format($invoice->file_size / 1024 / 1024, 2) }} MB</span>
                            </p>
                        </div>
                        <a href="{{ route('admin.invoices.download', $invoice) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-vd-primary hover:bg-vd-primary/90 text-white font-semibold text-xs transition-colors shrink-0">
                            Download
                        </a>
                    </div>
                @else
                    <div class="p-4 bg-gray-500/10 rounded-lg border border-gray-500/20">
                        <p class="text-sm text-gray-400">No file attached</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Assigned Users --}}
        <div class="vd-card border-[#2a3f5f] !p-6">
            <h2 class="text-xl font-bold text-white mb-6">Assigned Users</h2>
            @if ($invoice->users->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-[#2a3f5f]">
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">User</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Email</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Organization</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#2a3f5f]">
                            @foreach ($invoice->users as $user)
                                <tr class="hover:bg-white/5 transition-colors">
                                    <td class="px-4 py-3 font-medium text-white">{{ $user->name }}</td>
                                    <td class="px-4 py-3 text-gray-400">{{ $user->email }}</td>
                                    <td class="px-4 py-3 text-gray-400">
                                        {{ $user->organization?->name ?? 'Unassigned' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-4 bg-gray-500/10 rounded-lg border border-gray-500/20">
                    <p class="text-sm text-gray-400">No users assigned to this invoice</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="space-y-6">
        {{-- Details Card --}}
        <div class="vd-card border-[#2a3f5f] !p-6">
            <h3 class="text-lg font-bold text-white mb-4">Details</h3>
            <div class="space-y-4">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Status</p>
                    <p class="text-sm font-semibold text-white capitalize">{{ $invoice->status }}</p>
                </div>
                <div class="border-t border-[#2a3f5f] pt-4">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Active</p>
                     <x-badge :active="$invoice->is_active">
                        {{ $invoice->is_active ? 'Active' : 'Inactive' }}
                    </x-badge>
                </div>
                <div class="border-t border-[#2a3f5f] pt-4">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Download Expires</p>
                    <p class="text-sm text-gray-400">
                        {{ $invoice->download_expired_at?->format('M j, Y') ?? 'No limit' }}
                    </p>
                </div>
                <div class="border-t border-[#2a3f5f] pt-4">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Assigned Users</p>
                    <p class="text-sm font-semibold text-white">{{ $invoice->users->count() }}</p>
                </div>
                <div class="border-t border-[#2a3f5f] pt-4">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Created</p>
                    <p class="text-sm text-gray-400">
                        {{ $invoice->created_at->format('M j, Y H:i') }}
                    </p>
                </div>
                <div class="border-t border-[#2a3f5f] pt-4">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Last Updated</p>
                    <p class="text-sm text-gray-400">
                        {{ $invoice->updated_at->format('M j, Y H:i') }}
                    </p>
                </div>
            </div>
        </div>
</div>

@endsection
