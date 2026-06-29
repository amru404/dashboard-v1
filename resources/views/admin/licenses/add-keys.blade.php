@extends('layouts.admin')

@section('title', 'Add Sub-Product Keys - ' . $license->product->name)

@section('content')

<div class="mb-8">
    <a href="{{ route('admin.licenses.show', $license) }}" class="text-vd-primary hover:underline text-sm mb-4 inline-block">← Back to License</a>
    <h1 class="text-3xl font-bold text-white mb-2">Add Sub-Product Keys</h1>
    <p class="text-base text-gray-300">
        {{ $license->product->name }} - Assigned to {{ $license->user->name }}
    </p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Form Section -->
    <div class="lg:col-span-2">
        <div class="vd-card border-[#2a3f5f]">
            <form method="POST" action="{{ route('admin.licenses.store-keys', $license) }}" x-data="subProductKeyForm()">
                @csrf

                <!-- Sub-Products Selection -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-200 mb-3">
                        Select Sub-Products <span class="text-red-500">*</span>
                    </label>

                    <div class="space-y-2 max-h-64 overflow-y-auto border border-[#2a3f5f] rounded-lg p-3">
                        @php
                            $descendants = $license->product->getFlatDescendants();
                        @endphp

                        @if ($descendants->isEmpty())
                            <p class="text-gray-400 text-sm">No sub-products available for this product.</p>
                        @else
                            @foreach ($descendants as $subProduct)
                                <label class="flex items-center gap-3 p-2 hover:bg-[#0f1829]/50 rounded cursor-pointer">
                                    <input 
                                        type="checkbox"
                                        value="{{ $subProduct->id }}"
                                        class="w-4 h-4 rounded bg-[#0f1829] border-[#2a3f5f] text-vd-primary focus:ring-2 focus:ring-vd-primary sub-product-checkbox"
                                        @change="updateSubProducts()"
                                        data-product-id="{{ $subProduct->id }}"
                                    >
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-gray-300">
                                            {{ $license->product->name }}
                                            @for ($i = 0; $i < $subProduct->tree_depth; $i++)
                                                <span class="text-gray-500">→ sub</span>
                                            @endfor
                                        </p>
                                        <input 
                                            type="number"
                                            min="1"
                                            value="1"
                                            placeholder="Qty"
                                            class="mt-1 w-20 px-2 py-1 text-sm rounded bg-[#0f1829] border border-[#2a3f5f] text-white focus:outline-none focus:border-vd-primary qty-input"
                                            data-qty-for="{{ $subProduct->id }}"
                                            @change="updateSubProducts()"
                                            style="display: none;"
                                        >
                                    </div>
                                </label>
                            @endforeach
                        @endif
                    </div>

                    @error('sub_products')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Dynamic Key Input Fields -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-200 mb-3">
                        License Keys
                    </label>

                    <div id="key-forms-container" class="space-y-4">
                        <!-- Key forms will be generated here -->
                    </div>

                    @error('license_keys')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Summary -->
                <div class="mb-6 p-4 rounded-lg bg-blue-500/10 border border-blue-500/30">
                    <p class="text-sm text-blue-300">
                        <span class="font-semibold">Total keys to generate:</span> 
                        <span x-text="totalKeys" class="font-bold">0</span>
                    </p>
                </div>

                <!-- Actions -->
                <div class="flex gap-3">
                    <button 
                        type="submit"
                        class="flex-1 px-4 py-2.5 rounded-lg bg-vd-primary hover:bg-vd-primary/90 text-white font-semibold transition-colors"
                    >
                        Save Keys
                    </button>
                    <a 
                        href="{{ route('admin.licenses.show', $license) }}"
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
            <h3 class="text-lg font-semibold text-white mb-4">Information</h3>
            
            <div class="space-y-3 text-sm text-gray-300">
                <div>
                    <p class="font-semibold text-white">Parent Product</p>
                    <p class="text-sm">{{ $license->product->name }}</p>
                </div>

                <div>
                    <p class="font-semibold text-white">Customer</p>
                    <p class="text-sm">{{ $license->user->name }}</p>
                </div>

                <div>
                    <p class="font-semibold text-white">License Type</p>
                    <p class="text-sm">{{ $license->licenseType->name }}</p>
                </div>

                @if($license->max_activations)
                    <div>
                        <p class="font-semibold text-white">Device Limit</p>
                        <p class="text-sm">{{ $license->max_activations }}</p>
                    </div>
                @endif

                @if($license->expired_date)
                    <div>
                        <p class="font-semibold text-white">Expires</p>
                        <p class="text-sm">{{ $license->expired_date->format('M d, Y') }}</p>
                    </div>
                @endif
            </div>

            <hr class="border-[#2a3f5f] my-4">

            <div class="text-xs text-gray-400 space-y-2">
                <p>Select sub-products and set quantity for each. The form will auto-generate key input fields.</p>
                <p>All keys will be created under this parent product license.</p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('subProductKeyForm', () => ({
        selectedSubProducts: [],
        totalKeys: 0,

        init() {
            // Initialize on page load
        },

        updateSubProducts() {
            const checkboxes = document.querySelectorAll('.sub-product-checkbox:checked');
            this.selectedSubProducts = [];
            this.totalKeys = 0;

            checkboxes.forEach(checkbox => {
                const productId = checkbox.dataset.productId;
                const qtyInput = document.querySelector(`[data-qty-for="${productId}"]`);
                const qty = qtyInput ? parseInt(qtyInput.value) || 1 : 1;

                this.selectedSubProducts.push({ id: productId, qty });
                this.totalKeys += qty;

                // Show quantity input
                if (qtyInput) {
                    qtyInput.style.display = 'block';
                }
            });

            // Hide quantity inputs for unchecked items
            document.querySelectorAll('.sub-product-checkbox:not(:checked)').forEach(checkbox => {
                const productId = checkbox.dataset.productId;
                const qtyInput = document.querySelector(`[data-qty-for="${productId}"]`);
                if (qtyInput) {
                    qtyInput.style.display = 'none';
                }
            });

            this.renderKeyForms();
        },

        renderKeyForms() {
            const container = document.getElementById('key-forms-container');
            container.innerHTML = '';

            this.selectedSubProducts.forEach((subProduct, index) => {
                for (let i = 0; i < subProduct.qty; i++) {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'grid gap-3 p-3 bg-[#0f1829]/30 rounded-lg border border-[#2a3f5f]';
                    wrapper.innerHTML = `
                        <div>
                            <label class="block text-xs font-semibold text-gray-300 mb-2">
                                Sub-Product ${index + 1}, Key ${i + 1}
                            </label>
                            <div class="flex gap-2">
                                <input 
                                    type="text"
                                    name="license_keys[${subProduct.id}][]"
                                    placeholder="Enter or generate key"
                                    class="flex-1 px-3 py-2 text-sm rounded bg-[#0f1829] border border-[#2a3f5f] text-white placeholder-gray-500 focus:outline-none focus:border-vd-primary font-mono uppercase"
                                    autocomplete="off"
                                    spellcheck="false"
                                >
                                <button 
                                    type="button"
                                    class="px-3 py-2 rounded-lg bg-vd-primary hover:bg-vd-primary/90 text-white text-sm font-semibold transition-colors generate-key-btn"
                                    data-product-id="${subProduct.id}"
                                >
                                    Gen
                                </button>
                            </div>
                        </div>
                    `;

                    container.appendChild(wrapper);

                    // Attach generate button handler
                    const genBtn = wrapper.querySelector('.generate-key-btn');
                    const keyInput = wrapper.querySelector('input[type="text"]');
                    
                    genBtn.addEventListener('click', async (e) => {
                        e.preventDefault();
                        await this.generateKey(keyInput);
                    });
                }
            });
        },

        async generateKey(inputElement) {
            try {
                const response = await fetch('{{ route("admin.licenses.generate-key") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ quantity: 1 }),
                });

                if (!response.ok) throw new Error('Failed to generate key');

                const data = await response.json();
                inputElement.value = data.license_key || (data.license_keys ? data.license_keys[0] : '');
            } catch (error) {
                console.error('Error generating key:', error);
                // Fallback: generate client-side
                inputElement.value = this.generateFallbackKey();
            }
        },

        generateFallbackKey() {
            const generateSegment = () => {
                const bytes = new Uint8Array(2);
                if (window.crypto?.getRandomValues) {
                    window.crypto.getRandomValues(bytes);
                } else {
                    bytes[0] = Math.floor(Math.random() * 256);
                    bytes[1] = Math.floor(Math.random() * 256);
                }
                return Array.from(bytes)
                    .map(b => b.toString(16).padStart(2, '0'))
                    .join('')
                    .toUpperCase();
            };

            return Array.from({ length: 4 }, generateSegment).join('-');
        }
    }));
});
</script>

@endsection
