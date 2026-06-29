@props([
    'license',
])

@php
    $days = $license->daysUntilExpiry();
@endphp

<div class="bg-white/5 border border-[#2a3f5f] rounded-lg p-4 hover:border-[#3a5f7f] transition-colors">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
        <!-- Key Section -->
        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">License Key</p>
            <div class="flex items-center gap-2">
                <code 
                    class="font-mono text-sm text-gray-300 break-all"
                    data-license-key="{{ $license->id }}"
                    data-visible="false"
                    data-masked="{{ $license->masked_license_key }}"
                >
                    {{ $license->masked_license_key }}
                </code>
                <button
                    type="button"
                    data-toggle-key="{{ $license->id }}"
                    @click="toggleKeyVisibility({{ $license->id }})"
                    class="inline-flex items-center justify-center w-5 h-5 rounded text-gray-400 hover:text-gray-300 transition-colors flex-shrink-0"
                    title="Toggle visibility"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </button>
            </div>
            <input type="hidden" id="full-key-{{ $license->id }}" value="{{ $license->license_key }}" data-masked="{{ $license->masked_license_key }}">
        </div>

        <!-- Customer Section -->
        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Customer</p>
            <div class="space-y-1">
                <p class="text-sm font-medium text-white">{{ $license->user->name }}</p>
                <p class="text-xs text-gray-400">{{ $license->user->email }}</p>
                <p class="text-xs text-gray-400">{{ $license->client_name ?? $license->user->organization?->name ?? 'Unassigned' }}</p>
            </div>
        </div>

        <!-- License Details Section -->
        <div class="grid grid-cols-2 gap-2">
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Type</p>
                <p class="text-sm text-white">{{ $license->licenseType->name }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Quantity</p>
                <p class="text-sm text-white">{{ $license->quantity }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Device Limit</p>
                <p class="text-sm text-white">{{ $license->max_activations ?? '∞' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Active</p>
                <p class="text-sm text-white">{{ $license->active_activations_count }} / {{ $license->max_activations ?? '∞' }}</p>
            </div>
        </div>

        <!-- Expiry Section -->
        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Expiry</p>
            <div class="space-y-2">
                @if ($license->isExpired())
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-500/20 text-red-400 border border-red-500/30">Expired</span>
                @elseif ($days !== null && $days <= 30)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-orange-500/20 text-orange-400 border border-orange-500/30">{{ $days }}d left</span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-500/20 text-green-400 border border-green-500/30">{{ $license->expired_date ? 'Active' : 'No expiry' }}</span>
                @endif
                <p class="text-xs text-gray-400">{{ $license->expired_date?->format('M j, Y') ?? 'Never' }}</p>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex flex-wrap justify-end gap-2 pt-3 border-t border-[#2a3f5f]">
        <a href="{{ route('admin.licenses.show', $license) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-gray-300 hover:text-white transition-colors">View</a>
        <a href="{{ route('admin.licenses.edit', $license) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-gray-300 hover:text-white transition-colors">Edit</a>
        <form method="POST" action="{{ route('admin.licenses.destroy', $license) }}" onsubmit="return confirm('Delete this license? This will also remove related activation records.')" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-red-400 hover:text-red-300 transition-colors">Delete</button>
        </form>
    </div>
</div>
