@extends('layouts.admin')

@section('title', 'License Activations')

@section('content')

{{-- ── Page Header ── --}}
<div class="mb-8">
    <div class="flex items-start justify-between gap-4 mb-2">
        <div class="flex-1">
            <h1 class="text-3xl font-bold text-white mb-2">License Activations</h1>
            <p class="text-base text-gray-300">
                Registered customer devices for issued licenses.
            </p>
        </div>
    </div>
</div>

<div class="vd-card  border-[#2a3f5f] overflow-hidden !p-0">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-[#0f1829]/30 border-b border-[#2a3f5f]">
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Device</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#2a3f5f]">
                @forelse ($activations as $activation)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium text-white">{{ $activation->device_id }}</p>
                            <p class="mt-1 text-sm text-gray-400">{{ $activation->hostname ?? 'No hostname' }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium text-white">{{ $activation->license->user->name }}</p>
                            <p class="mt-1 text-sm text-gray-400">{{ $activation->license->user->organization?->name ?? 'Unassigned' }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-400">{{ $activation->license->product->name }}</td>
                        <td class="px-6 py-4">
                            @if ($activation->status === 'active')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-500/20 text-green-400 border border-green-500/30">{{ ucfirst($activation->status) }}</span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-500/20 text-gray-400 border border-gray-500/30">{{ ucfirst($activation->status) }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <form method="POST" action="{{ route('admin.license-activations.destroy', $activation) }}" onsubmit="return confirm('Reset this activation?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-red-400 hover:text-red-300 transition-colors">Reset</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-400">No license activations have been recorded.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="border-t border-[#2a3f5f] px-6 py-4">
        {{ $activations->links() }}
    </div>
</div>
@endsection
