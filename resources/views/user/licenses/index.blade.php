@extends('layouts.user')

@section('title', 'My Licenses')

@section('content')

    <x-page-header title="License Keys" subtitle="Browse your software products and license keys.">
    </x-page-header>

    {{-- Search & Filter Bar --}}
    <div class="mb-6 rounded-lg border border-[#2a3f5f] bg-[#0f1829]/30 p-4">
        <div class="grid gap-3 sm:grid-cols-[1fr_auto]">
            <div class="relative">
                <input 
                    type="text" 
                    id="licenseSearch"
                    placeholder="Search by product name, code, or license key..."
                    class="w-full px-4 py-2.5 pl-10 rounded-lg bg-[#0f1829] border border-[#2a3f5f] text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-vd-primary/50 focus:border-vd-primary transition-colors text-sm" />
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            
            <select 
                id="licenseStatus" 
                class="px-4 py-2.5 rounded-lg bg-[#0f1829] border border-[#2a3f5f] text-white focus:outline-none focus:ring-2 focus:ring-vd-primary/50 focus:border-vd-primary transition-colors text-sm min-w-[140px]">
                <option value="all">All Status</option>
                <option value="active">Active</option>
                <option value="expired">Expired</option>
            </select>
        </div>
    </div>

    {{-- License Table by Product --}}
    <div class="space-y-4" x-data="licenseAccordion">
        @forelse ($rootProducts as $product)
            @php
                // Get all licenses for this product (direct + from all descendants)
                $allLicenses = $product->licenses->merge(
                    $product->allChildren->flatMap(fn($child) => $child->licenses)
                );
                $accordionId = "accordion-product-{$product->id}";
            @endphp

            <!-- Parent Product Section -->
            <div class="rounded-lg border border-[#2a3f5f] overflow-hidden">
                <!-- Product Header (Accordion Toggle) -->
                <button
                    @click="toggleAccordion('{{ $accordionId }}')"
                    class="w-full bg-gradient-to-r from-[#0f1829]/50 to-[#1a3a52]/30 border-b border-[#2a3f5f] p-4 flex items-start justify-between gap-4 hover:bg-gradient-to-r hover:from-[#0f1829]/60 hover:to-[#1a3a52]/40 transition-colors text-left"
                >
                    <div class="flex-1 flex items-start justify-between gap-4">
                        <div class="flex-1 flex items-start gap-3 min-w-0">
                            <svg
                                class="w-5 h-5 text-gray-400 transition-transform flex-shrink-0 mt-0.5"
                                :class="isExpanded('{{ $accordionId }}') ? 'rotate-90' : ''"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                stroke-width="2"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                            <div class="min-w-0 flex-1">
                                <h2 class="text-lg font-semibold text-white">{{ $product->name }}</h2>
                                @if ($product->code)
                                    <p class="text-sm text-gray-400">{{ $product->code }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-500/20 text-green-400 border border-green-500/30">
                                {{ $allLicenses->count() }} license{{ $allLicenses->count() !== 1 ? 's' : '' }}
                            </span>
                        </div>
                    </div>
                </button>

                <!-- Licenses Table (Accordion Content) -->
                <div
                    x-show="isExpanded('{{ $accordionId }}')"
                    x-collapse
                    class="bg-[#0f1829]/20"
                >
                    @if ($allLicenses->isNotEmpty())
                        <div class="overflow-x-auto">
                            <table class="w-full divide-y divide-[#2a3f5f]" x-data="licenseTable">
                                <thead class="bg-[#0f1829]/70 border-b border-[#2a3f5f]">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Product Type</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">License Key</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Type</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Expiry</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Status</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Activation</th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-300 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#2a3f5f]">
                                    @foreach ($allLicenses as $license)
                                        @php
                                            $days = $license->daysUntilExpiry();
                                        @endphp
                                        <tr class="hover:bg-[#0f1829]/30 transition-colors">
                                            <!-- Product Type -->
                                            <td class="px-4 py-3 text-sm">
                                                @if ($license->subProduct)
                                                    <div class="flex items-center gap-2">
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-purple-500/20 text-purple-300 border border-purple-500/30">
                                                            Sub
                                                        </span>
                                                        <span class="text-gray-200 font-medium">{{ $license->subProduct->name }}</span>
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

                                            {{-- activation --}}
                                            <td class="px-4 py-3 text-sm">
                                                <span class="inline-flex items-center rounded-full border border-vd-border bg-blue-500/10 px-3 py-1 text-xs font-semibold text-blue-400">
                                                    {{ $license->active_activations_count }} / {{ $license->max_activations ?: '∞' }}
                                                </span>
                                            </td>

                                            <!-- Actions -->
                                            <td class="px-4 py-3 text-sm text-right">
                                                <div class="flex items-center justify-end gap-2">
                                                    <a 
                                                        href="{{ route('user.licenses.show', $license) }}" 
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
                    @else
                        <div class="bg-[#0f1829]/50 p-6 text-center">
                            <p class="text-sm text-gray-400">No licenses for this product</p>
                        </div>
                    @endif
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

    {{-- No Results Message (for filtering) --}}
    <div id="noLicenseResults" class="hidden rounded-lg border border-dashed border-[#2a3f5f] bg-[#0f1829]/30 p-12 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <p class="mt-4 text-sm text-gray-400">No licenses found matching your search criteria.</p>
    </div>

    @if ($rootProducts->hasPages())
        <div class="mt-8">
            {{ $rootProducts->links() }}
        </div>
    @endif

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

@vite('resources/js/license-filter.js')
