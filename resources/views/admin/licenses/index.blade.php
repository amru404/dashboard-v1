@extends('layouts.admin')

@section('title', 'Licenses')

@section('content')

    <x-page-header title="Licenses" subtitle="Installer-facing license records assigned to customer users.">
        <x-slot name="actions">
            <x-button :href="route('admin.licenses.create')" variant="primary">
                New Licenses
            </x-button>
        </x-slot>
    </x-page-header>


@php
    // Group licenses by user first
    $licensesByUser = $licenses->groupBy('user_id');
@endphp

<div class="space-y-4" x-data="licenseAccordion">
    @forelse ($licensesByUser as $userId => $userLicenses)
        @php
            $user = $userLicenses->first()->user;
            // Group by parent product for this user
            $licensesByProduct = $userLicenses->groupBy('product_id');
        @endphp

        <!-- User Section -->
        <div class="rounded-lg border border-[#2a3f5f] overflow-hidden">
            <!-- User Header -->
            <div class="bg-gradient-to-r from-[#0f1829]/50 to-[#1a3a52]/30 border-b border-[#2a3f5f] p-4">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-vd-primary/20 flex items-center justify-center flex-shrink-0">
                                <span class="text-sm font-bold text-vd-primary">{{ substr($user->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-white">{{ $user->name }}</h2>
                                <p class="text-sm text-gray-400">{{ $user->email }}</p>
                                @if ($user->organization)
                                    <p class="text-xs text-gray-500">{{ $user->organization->name }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-500/20 text-blue-400 border border-blue-500/30">
                            {{ $userLicenses->count() }} license{{ $userLicenses->count() !== 1 ? 's' : '' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Products Accordion -->
            <div class="divide-y divide-[#2a3f5f]">
                @foreach ($licensesByProduct as $productId => $productLicenses)
                    @php
                        $product = $productLicenses->first()->product;
                        $accordionId = "accordion-user-{$userId}-product-{$productId}";
                    @endphp

                    <div class="bg-[#0f1829]/20">
                        <!-- Product Header (Accordion Toggle) -->
                        <button
                            @click="toggleAccordion('{{ $accordionId }}')"
                            class="w-full px-4 py-3 flex items-center justify-between hover:bg-[#0f1829]/40 transition-colors"
                        >
                            <div class="flex items-center gap-3 flex-1 text-left">
                                <svg
                                    class="w-5 h-5 text-gray-400 transition-transform flex-shrink-0"
                                    :class="isExpanded('{{ $accordionId }}') ? 'rotate-90' : ''"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                    stroke-width="2"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                                <div>
                                    <h3 class="text-sm font-semibold text-white">{{ $product->name }}</h3>
                                    @if ($product->code)
                                        <p class="text-xs text-gray-500">{{ $product->code }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.licenses.generate-bulk', ['user_id' => $userId, 'product_id' => $productId]) }}" 
                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-vd-primary hover:bg-vd-primary/90 text-white transition-colors"
                                   onclick="event.stopPropagation()"
                                   title="Generate bulk licenses for sub-products">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Key
                                </a>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-500/20 text-green-400 border border-green-500/30">
                                    {{ $productLicenses->count() }}
                                </span>
                            </div>
                        </button>

                        <!-- Product Licenses Table (Accordion Content) -->
                        <div
                            x-show="isExpanded('{{ $accordionId }}')"
                            x-collapse
                            class="bg-[#0f1829]/50"
                        >
                            <div class="overflow-x-auto">
                                <table class="w-full divide-y divide-[#2a3f5f]" x-data="licenseTable">
                                    <thead class="bg-[#0f1829]/70 border-b border-[#2a3f5f]">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Sub-Product</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">License Key</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Type</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Expiry</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Status</th>
                                             <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Activations</th>
                                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-300 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-[#2a3f5f]">
                                        @foreach ($productLicenses as $license)
                                            @php
                                                $days = $license->daysUntilExpiry();
                                                $subProductPath = '';
                                                if ($license->subProduct) {
                                                    $breadcrumbs = $license->subProduct->getBreadcrumbs();
                                                    $subProductPath = $breadcrumbs->skip(1)->map(fn($p) => $p->name)->implode(' → ');
                                                    if (!$subProductPath) {
                                                        $subProductPath = $license->subProduct->name;
                                                    }
                                                }
                                            @endphp
                                            <tr class="hover:bg-[#0f1829]/30 transition-colors">
                                                <!-- Sub-Product Name -->
                                                <td class="px-4 py-3 text-sm">
                                                    @if ($license->subProduct)
                                                        <div class="flex items-center gap-2">
                                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-purple-500/20 text-purple-300 border border-purple-500/30">
                                                                Sub
                                                            </span>
                                                            <span class="text-gray-200 font-medium">{{ $subProductPath }}</span>
                                                        </div>
                                                    @else
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-blue-500/20 text-blue-300 border border-blue-500/30">
                                                            Parent
                                                        </span>
                                                    @endif
                                                </td>

                                                <!-- License Key with Toggle -->
                                                <td class="px-4 py-3 text-sm">
                                                    <div class="flex items-center gap-2">
                                                        <code 
                                                            class="font-mono text-xs text-gray-300 break-all"
                                                            :data-license-key="{{ $license->id }}"
                                                            :data-visible="false"
                                                            :data-masked="'{{ $license->masked_license_key }}'"
                                                        >
                                                            {{ $license->masked_license_key }}
                                                        </code>
                                                        <button
                                                            type="button"
                                                            @click="toggleKeyVisibility({{ $license->id }})"
                                                            class="inline-flex items-center justify-center w-5 h-5 rounded text-gray-400 hover:text-gray-300 transition-colors flex-shrink-0"
                                                            title="Toggle key visibility"
                                                        >
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                            </svg>
                                                        </button>
                                                        <input type="hidden" :id="'full-key-' + {{ $license->id }}" value="{{ $license->license_key }}" :data-masked="'{{ $license->masked_license_key }}'">
                                                    </div>
                                                </td>

                                                <!-- License Type -->
                                                <td class="px-4 py-3 text-sm">
                                                    <span class="text-gray-300">{{ $license->licenseType->name }}</span>
                                                </td>

                                                <!-- Expiry Date -->
                                                <td class="px-4 py-3 text-sm">
                                                    <span class="text-gray-300">{{ $license->expired_date?->format('M j, Y') ?? 'Never' }}</span>
                                                </td>

                                                <!-- Status -->
                                                <td class="px-4 py-3 text-sm">
                                                    @if ($license->isExpired())
                                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-500/20 text-red-400 border border-red-500/30">
                                                            Expired
                                                        </span>
                                                    @elseif ($days !== null && $days <= 30)
                                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-orange-500/20 text-orange-400 border border-orange-500/30">
                                                            {{ $days }}d left
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-500/20 text-green-400 border border-green-500/30">
                                                            {{ $license->expired_date ? 'Active' : 'No expiry' }}
                                                        </span>
                                                    @endif
                                                </td>

                                                {{-- Activation --}}
                                                 <td class="px-4 py-3 text-sm">
                                                     <span class="inline-flex items-center rounded-full border border-vd-border bg-blue-500/10 px-3 py-1 text-xs font-semibold text-blue-400">
                                                        {{ $license->active_activations_count }} / {{ $license->max_activations ?: '∞' }}
                                                    </span>
                                                 </td>
                                                <!-- Actions -->
                                                <td class="px-4 py-3 text-sm text-right">
                                                    <div class="flex items-center justify-end gap-2">
                                                        <a 
                                                            href="{{ route('admin.licenses.edit', $license) }}" 
                                                            class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-semibold text-gray-300 hover:text-white hover:bg-[#1a3a52] transition-colors"
                                                            title="Edit license"
                                                        >
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                            </svg>
                                                            Edit
                                                        </a>
                                                        <a 
                                                            href="{{ route('admin.licenses.show', $license) }}" 
                                                            class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-semibold text-gray-300 hover:text-white hover:bg-[#1a3a52] transition-colors"
                                                            title="View license details"
                                                        >
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                            </svg>
                                                            View
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <div class="rounded-lg border border-dashed border-[#2a3f5f] bg-[#0f1829]/30 p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="mt-4 text-sm text-gray-400">No licenses have been issued yet.</p>
        </div>
    @endforelse
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('licenseAccordion', () => ({
        expandedItems: [],

        toggleAccordion(id) {
            const index = this.expandedItems.indexOf(id);
            if (index > -1) {
                this.expandedItems.splice(index, 1);
            } else {
                this.expandedItems.push(id);
            }
        },

        isExpanded(id) {
            return this.expandedItems.includes(id);
        }
    }));

    Alpine.data('licenseTable', () => ({
        toggleKeyVisibility(licenseId) {
            const codeElement = document.querySelector(`[data-license-key="${licenseId}"]`);
            const fullKeyInput = document.getElementById(`full-key-${licenseId}`);
            
            if (!codeElement || !fullKeyInput) return;
            
            const isVisible = codeElement.getAttribute('data-visible') === 'true';
            
            if (isVisible) {
                codeElement.textContent = fullKeyInput.getAttribute('data-masked');
                codeElement.setAttribute('data-visible', 'false');
            } else {
                codeElement.textContent = fullKeyInput.value;
                codeElement.setAttribute('data-visible', 'true');
            }
        }
    }));
});
</script>

@endsection


