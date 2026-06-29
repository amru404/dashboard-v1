@extends('layouts.admin')

@section('title', 'Generate Bulk Licenses')

@section('content')

<div class="mb-8">
    <div class="flex items-start justify-between gap-4">
        <div class="flex-1">
            <h1 class="text-3xl font-bold text-white mb-2">Generate Bulk Licenses</h1>
            <p class="text-base text-gray-300">
                Generate multiple license keys for products and sub-products
            </p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Form Section -->
    <div class="lg:col-span-2">
        <div class="vd-card border-[#2a3f5f]">
            <form method="POST" action="{{ route('admin.licenses.bulk-store') }}" x-data="bulkLicenseForm()">
                @csrf

                <!-- User Selection -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-200 mb-2">
                        Assign to User <span class="text-red-500">*</span>
                    </label>
                    <select 
                        name="user_id"
                        required
                        @change="updateSelectedUser()"
                        class="w-full px-4 py-2 rounded-lg bg-[#0f1829] border border-[#2a3f5f] text-white placeholder-gray-500 focus:outline-none focus:border-vd-primary transition-colors"
                    >
                        <option value="">Select a user...</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">
                                {{ $user->name }} @if($user->organization) ({{ $user->organization->name }}) @endif
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- License Type -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-200 mb-2">
                        License Type <span class="text-red-500">*</span>
                    </label>
                    <select 
                        name="license_type_id"
                        required
                        class="w-full px-4 py-2 rounded-lg bg-[#0f1829] border border-[#2a3f5f] text-white placeholder-gray-500 focus:outline-none focus:border-vd-primary transition-colors"
                    >
                        <option value="">Select license type...</option>
                        @foreach ($licenseTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                    @error('license_type_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Device Limit -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-200 mb-2">
                        Device Limit
                    </label>
                    <input 
                        type="number"
                        name="max_activations"
                        placeholder="Leave blank for unlimited"
                        min="1"
                        class="w-full px-4 py-2 rounded-lg bg-[#0f1829] border border-[#2a3f5f] text-white placeholder-gray-500 focus:outline-none focus:border-vd-primary transition-colors"
                    >
                    @error('max_activations')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Expiry Date -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-200 mb-2">
                        Expiry Date
                    </label>
                    <input 
                        type="date"
                        name="expired_date"
                        class="w-full px-4 py-2 rounded-lg bg-[#0f1829] border border-[#2a3f5f] text-white placeholder-gray-500 focus:outline-none focus:border-vd-primary transition-colors"
                    >
                    <p class="text-xs text-gray-400 mt-1">Leave blank if license never expires</p>
                    @error('expired_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Products Section -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-200 mb-3">
                        Products <span class="text-red-500">*</span>
                    </label>

                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        @foreach ($products as $product)
                            @php
                                $descendants = $product->getFlatDescendants();
                            @endphp

                            <!-- Parent Product -->
                            <div class="rounded-lg border border-[#2a3f5f] overflow-hidden">
                                <div class="bg-[#0f1829]/50 p-4 flex items-center gap-3">
                                    <div class="flex-1">
                                        <p class="font-semibold text-white">{{ $product->name }}</p>
                                        @if($product->code)
                                            <p class="text-xs text-gray-400">{{ $product->code }}</p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Sub Products -->
                                @if($descendants->isNotEmpty())
                                    <div class="bg-[#0f1829]/30 space-y-3 p-3">
                                        @foreach ($descendants as $subProduct)
                                            <div class="flex items-center gap-3">
                                                <input 
                                                    type="checkbox"
                                                    class="w-4 h-4 rounded bg-[#0f1829] border-[#2a3f5f] text-vd-primary focus:ring-2 focus:ring-vd-primary product-checkbox"
                                                    data-product-id="{{ $subProduct->id }}"
                                                    data-parent-id="{{ $product->id }}"
                                                    @change="updateProductSelection()"
                                                >
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm text-gray-300">
                                                        {{ $product->name }}
                                                        @for ($i = 0; $i < $subProduct->tree_depth; $i++)
                                                            <span class="text-gray-500">→ sub</span>
                                                        @endfor
                                                    </p>
                                                </div>
                                                <input 
                                                    type="number"
                                                    min="1"
                                                    value="1"
                                                    placeholder="1"
                                                    class="w-20 px-2 py-1 text-sm rounded bg-[#0f1829] border border-[#2a3f5f] text-white focus:outline-none focus:border-vd-primary qty-input"
                                                    data-qty-product="{{ $subProduct->id }}"
                                                    @change="updateProductSelection()"
                                                    style="display: none;"
                                                >
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    @error('products')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Summary -->
                <div class="mb-6 p-4 rounded-lg bg-blue-500/10 border border-blue-500/30">
                    <p class="text-sm text-blue-300">
                        <span class="font-semibold">Total licenses to generate:</span> 
                        <span x-text="totalLicenses" class="font-bold">0</span>
                    </p>
                </div>

                <!-- Actions -->
                <div class="flex gap-3">
                    <button 
                        type="submit"
                        class="flex-1 px-4 py-2.5 rounded-lg bg-vd-primary hover:bg-vd-primary/90 text-white font-semibold transition-colors"
                    >
                        Generate Licenses
                    </button>
                    <a 
                        href="{{ route('admin.licenses.index') }}"
                        class="px-4 py-2.5 rounded-lg bg-[#0f1829] border border-[#2a3f5f] text-gray-300 font-semibold hover:bg-[#1a2942] transition-colors"
                    >
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Info Sidebar -->
    <div class="lg:col-span-1">
        <div class="vd-card border-[#2a3f5f]">
            <h3 class="text-lg font-semibold text-white mb-4">How it works</h3>
            
            <div class="space-y-4 text-sm text-gray-300">
                <div>
                    <p class="font-semibold text-white mb-1">1. Select User</p>
                    <p>Choose which customer user will own these licenses</p>
                </div>

                <div>
                    <p class="font-semibold text-white mb-1">2. Select Products</p>
                    <p>Check the products/sub-products you want to license. Set quantity for each.</p>
                </div>

                <div>
                    <p class="font-semibold text-white mb-1">3. Configure License</p>
                    <p>Set the license type, device limit, and expiry date</p>
                </div>

                <div>
                    <p class="font-semibold text-white mb-1">4. Generate</p>
                    <p>System will create the licenses with auto-generated keys</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('bulkLicenseForm', () => ({
        totalLicenses: 0,

        init() {
            // Initialize form for bulk generation
        },

        updateProductSelection() {
            const checkboxes = document.querySelectorAll('.product-checkbox:checked');
            this.totalLicenses = 0;

            // Remove existing hidden inputs
            document.querySelectorAll('[data-hidden-product]').forEach(el => el.remove());

            checkboxes.forEach(checkbox => {
                const productId = checkbox.dataset.productId;
                const qtyInput = document.querySelector(`[data-qty-product="${productId}"]`);
                const qty = qtyInput ? parseInt(qtyInput.value) || 1 : 1;
                
                // Show quantity input when checked
                if (qtyInput) {
                    qtyInput.style.display = 'block';
                }

                this.totalLicenses += qty;

                // Add hidden input for form submission
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'products[]';
                input.value = JSON.stringify({
                    product_id: productId,
                    quantity: qty
                });
                input.dataset.hiddenProduct = productId;
                document.querySelector('form').appendChild(input);
            });

            // Hide quantity inputs for unchecked items
            document.querySelectorAll('.product-checkbox:not(:checked)').forEach(checkbox => {
                const productId = checkbox.dataset.productId;
                const qtyInput = document.querySelector(`[data-qty-product="${productId}"]`);
                if (qtyInput) {
                    qtyInput.style.display = 'none';
                }
            });
        },

        updateSelectedUser() {
            // Can be used for additional logic if needed
        }
    }));
});
</script>

@endsection
