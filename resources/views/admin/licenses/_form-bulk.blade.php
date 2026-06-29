 @csrf

    <!-- Sub-products Section -->
    <div class="rounded-lg border border-[#2a3f5f] overflow-hidden bg-[#0f1829]/30">
        <div class="bg-gradient-to-r from-[#0f1829]/50 to-[#1a3a52]/30 border-b border-[#2a3f5f] p-4">
            <h2 class="text-lg font-semibold text-white">Sub-products</h2>
            <p class="text-sm text-gray-400 mt-1"><span x-text="selectedCount"></span> selected</p>
        </div>

        <div class="p-6 space-y-4">
            <!-- User Selection -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Customer User</label>
                <select 
                    name="user_id" 
                    x-model="userId"
                    @change="loadUserProducts"
                    required
                    class="w-full rounded-lg border border-[#2a3f5f] bg-[#0f1829] px-4 py-3 text-sm text-white focus:border-vd-primary focus:ring-2 focus:ring-vd-primary/20"
                >
                    <option value="">Select customer</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>
                            {{ $user->name }} - {{ $user->email }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('user_id')" class="mt-2" />
            </div>

            <!-- Parent Product Selection -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Parent Product</label>
                <select 
                    name="product_id" 
                    x-model="productId"
                    @change="loadSubProducts"
                    required
                    class="w-full rounded-lg border border-[#2a3f5f] bg-[#0f1829] px-4 py-3 text-sm text-white focus:border-vd-primary focus:ring-2 focus:ring-vd-primary/20"
                >
                    <option value="">Select parent product</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}" @selected(request('product_id') == $product->id)>
                            {{ $product->name }} @if($product->code) - {{ $product->code }} @endif
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('product_id')" class="mt-2" />
            </div>

            <!-- Sub-products List -->
            <div x-show="subProducts.length > 0" class="space-y-2">
                <template x-for="(subProduct, index) in subProducts" :key="subProduct.id">
                    <div class="rounded-lg border p-4 transition-colors"
                         :style="`margin-left: ${subProduct.depth * 16}px`"
                         :class="subProduct.selected ? 'border-blue-500 bg-blue-500/5' : 'border-[#2a3f5f] bg-[#0f1829]/50'">
                        <div class="flex items-center justify-between gap-4">
                            <label class="flex items-center gap-3 flex-1 cursor-pointer">
                                <input 
                                    type="checkbox" 
                                    x-model="subProduct.selected"
                                    @change="updateSelection"
                                    class="w-4 h-4 rounded border-gray-600 bg-[#0f1829] text-vd-primary focus:ring-vd-primary focus:ring-offset-0"
                                >
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <p class="text-sm font-medium text-white" x-text="subProduct.name"></p>
                                        <span class="text-xs text-gray-500 whitespace-nowrap" x-text="subProduct.breadcrumb ? `is ${subProduct.breadcrumb}` : ''"></span>
                                    </div>
                                    <p class="text-xs text-gray-400" x-text="subProduct.code"></p>
                                </div>
                                <span class="px-2.5 py-1 rounded text-xs font-medium bg-purple-500/20 text-purple-300 border border-purple-500/30 whitespace-nowrap flex-shrink-0"
                                      x-text="subProduct.depthLabel">
                                </span>
                            </label>
                            
                            <div x-show="subProduct.selected" class="flex items-center gap-2 flex-shrink-0">
                                <button 
                                    type="button"
                                    @click="decreaseQty(index)"
                                    class="w-8 h-8 rounded-lg border border-[#2a3f5f] bg-[#0f1829] text-gray-300 hover:bg-[#1a3a52] hover:text-white transition-colors flex items-center justify-center"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/>
                                    </svg>
                                </button>
                                <span class="w-12 text-center text-white font-semibold" x-text="subProduct.quantity"></span>
                                <button 
                                    type="button"
                                    @click="increaseQty(index)"
                                    class="w-8 h-8 rounded-lg border border-[#2a3f5f] bg-[#0f1829] text-gray-300 hover:bg-[#1a3a52] hover:text-white transition-colors flex items-center justify-center"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <div x-show="productId && subProducts.length === 0" class="text-center py-8 text-gray-400">
                <p>No sub-products available for this parent product</p>
            </div>
        </div>
    </div>

    <!-- Configure License Section -->
    <div class="rounded-lg border border-[#2a3f5f] overflow-hidden bg-[#0f1829]/30">
        <div class="bg-gradient-to-r from-[#0f1829]/50 to-[#1a3a52]/30 border-b border-[#2a3f5f] p-4">
            <h2 class="text-lg font-semibold text-white">Configure License</h2>
            <p class="text-sm text-gray-400 mt-1">Set the license type, device limit, and expiry date</p>
        </div>

        <div class="p-6 space-y-4">
            <!-- License Type -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">License type</label>
                <select 
                    name="license_type_id" 
                    required
                    class="w-full rounded-lg border border-[#2a3f5f] bg-[#0f1829] px-4 py-3 text-sm text-white focus:border-vd-primary focus:ring-2 focus:ring-vd-primary/20"
                >
                    <option value="">Select type</option>
                    @foreach ($licenseTypes as $licenseType)
                        <option value="{{ $licenseType->id }}">
                            {{ $licenseType->name }} ({{ $licenseType->code }})
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('license_type_id')" class="mt-2" />
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <!-- Max Activations -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Max activations</label>
                    <input 
                        type="number" 
                        name="max_activations" 
                        min="1"
                        placeholder="Unlimited if blank"
                        class="w-full rounded-lg border border-[#2a3f5f] bg-[#0f1829] px-4 py-3 text-sm text-white placeholder-gray-500 focus:border-vd-primary focus:ring-2 focus:ring-vd-primary/20"
                    >
                    <x-input-error :messages="$errors->get('max_activations')" class="mt-2" />
                </div>

                <!-- Expiry Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Expiry date</label>
                    <input 
                        type="date" 
                        name="expired_date"
                        class="w-full rounded-lg border border-[#2a3f5f] bg-[#0f1829] px-4 py-3 text-sm text-white focus:border-vd-primary focus:ring-2 focus:ring-vd-primary/20"
                    >
                    <p class="text-xs text-gray-500 mt-1">Leave blank for open-ended.</p>
                    <x-input-error :messages="$errors->get('expired_date')" class="mt-2" />
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Section -->
    <div class="rounded-lg border border-[#2a3f5f] overflow-hidden bg-[#0f1829]/30">
        <div class="bg-gradient-to-r from-[#0f1829]/50 to-[#1a3a52]/30 border-b border-[#2a3f5f] p-4">
            <h2 class="text-lg font-semibold text-white">Summary</h2>
        </div>

        <div class="p-6">
            <div class="grid gap-4 md:grid-cols-3">
                <div class="rounded-lg border border-[#2a3f5f] bg-[#0f1829]/50 p-4">
                    <p class="text-xs text-gray-400 mb-1">Sub-products</p>
                    <p class="text-2xl font-bold text-white" x-text="selectedCount"></p>
                </div>
                <div class="rounded-lg border border-[#2a3f5f] bg-[#0f1829]/50 p-4">
                    <p class="text-xs text-gray-400 mb-1">Total keys</p>
                    <p class="text-2xl font-bold text-vd-primary" x-text="totalKeys"></p>
                </div>
                <div class="rounded-lg border border-[#2a3f5f] bg-[#0f1829]/50 p-4">
                    <p class="text-xs text-gray-400 mb-1">License type</p>
                    <p class="text-sm font-medium text-white" x-text="selectedLicenseType || 'Not selected'"></p>
                </div>
            </div>
        </div>
    </div>

    <!-- License Keys Section -->
    <div x-show="totalKeys > 0" class="rounded-lg border border-[#2a3f5f] overflow-hidden bg-[#0f1829]/30">
        <div class="bg-gradient-to-r from-[#0f1829]/50 to-[#1a3a52]/30 border-b border-[#2a3f5f] p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-white">License keys</h2>
                    <p class="text-sm text-gray-400 mt-1"><span x-text="totalKeys"></span> keys across <span x-text="selectedCount"></span> sub-products</p>
                </div>
                <button 
                    type="button"
                    @click="generateAllKeys"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-vd-primary hover:bg-vd-primary/90 text-white font-semibold transition-colors"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                    Generate all
                </button>
            </div>
        </div>

        <div class="p-6 space-y-6">
            <template x-for="(subProduct, spIndex) in subProducts" :key="subProduct.id">
                <div x-show="subProduct.selected && subProduct.quantity > 0" class="space-y-4 rounded-lg border border-[#2a3f5f] bg-[#0f1829]/50 p-4">
                    <!-- Sub-product Header -->
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-semibold text-white" x-text="subProduct.name"></h3>
                            <p class="text-xs text-gray-400" x-text="subProduct.code"></p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-blue-500/20 text-blue-400 border border-blue-500/30">
                                <span x-text="`${subProduct.quantity} key${subProduct.quantity > 1 ? 's' : ''}`"></span>
                            </span>
                            <button 
                                type="button"
                                @click="generateSubProductKeys(spIndex)"
                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-vd-primary/20 text-vd-primary hover:bg-vd-primary/30 transition-colors text-xs font-medium"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                </svg>
                                Generate
                            </button>
                        </div>
                    </div>

                    <!-- License Key Inputs -->
                    <div class="space-y-3">
                        <template x-for="(key, keyIndex) in subProduct.keys" :key="`${subProduct.id}-${keyIndex}`">
                            <div class="flex items-center gap-2">
                                <div class="w-10 h-10 rounded-lg border border-[#2a3f5f] bg-[#0f1829] flex items-center justify-center flex-shrink-0">
                                    <span class="text-xs font-semibold text-gray-500" x-text="keyIndex + 1"></span>
                                </div>
                                <input 
                                    type="text"
                                    x-model="subProduct.keys[keyIndex]"
                                    @input="subProduct.keys[keyIndex] = $event.target.value.toUpperCase()"
                                    class="flex-1 font-mono text-sm rounded-lg border border-[#2a3f5f] bg-[#0f1829] px-4 py-2.5 text-white uppercase focus:border-vd-primary focus:ring-2 focus:ring-vd-primary/20"
                                    placeholder="Type or generate license key"
                                    required
                                >
                                <button 
                                    type="button"
                                    @click="generateSingleKey(spIndex, keyIndex)"
                                    class="px-4 py-2.5 rounded-lg border border-[#2a3f5f] bg-[#0f1829] text-gray-300 hover:bg-[#1a3a52] hover:text-white transition-colors font-medium flex-shrink-0"
                                    title="Generate this key"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                    </svg>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Hidden fields for form submission -->
    <template x-for="(subProduct, index) in subProducts" :key="subProduct.id">
        <template x-if="subProduct.selected && subProduct.keys.length > 0">
            <template x-for="(key, keyIndex) in subProduct.keys" :key="`hidden-${subProduct.id}-${keyIndex}`">
                <input type="hidden" :name="`licenses[${index}][${keyIndex}][sub_product_id]`" :value="subProduct.id">
                <input type="hidden" :name="`licenses[${index}][${keyIndex}][license_key]`" :value="key">
            </template>
        </template>
    </template>

    <!-- Action Buttons -->
    <div class="flex gap-3">
        <button 
            type="submit"
            x-show="totalKeys > 0"
            :disabled="!canSubmit"
            class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-lg bg-green-600 hover:bg-green-700 disabled:bg-gray-600 disabled:cursor-not-allowed text-white font-semibold transition-colors"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            <span>Generate <span x-text="totalKeys"></span> Licenses</span>
        </button>
        <a href="{{ route('admin.licenses.index') }}" class="inline-flex items-center justify-center px-6 py-3 rounded-lg border border-[#2a3f5f] bg-[#0f1829] text-gray-300 hover:bg-[#1a3a52] hover:text-white font-semibold transition-colors">
            Cancel
        </a>
    </div>

    <script>
document.addEventListener('alpine:init', () => {
    Alpine.data('bulkLicenseGenerator', () => ({
        userId: '{{ request("user_id") }}',
        productId: '{{ request("product_id") }}',
        subProducts: [],
        keyLength: {{ (int)$licenseKeyLength }},
        
        init() {
            if (this.userId && this.productId) {
                this.loadSubProducts();
            }

            // Listen to license type select
            const licenseTypeSelect = document.querySelector('select[name="license_type_id"]');
            if (licenseTypeSelect) {
                licenseTypeSelect.addEventListener('change', (e) => {
                    const option = e.target.options[e.target.selectedIndex];
                    this.selectedLicenseType = option ? option.text : 'Not selected';
                });
            }
        },

        get selectedCount() {
            return this.subProducts.filter(sp => sp.selected).length;
        },

        get totalKeys() {
            return this.subProducts
                .filter(sp => sp.selected)
                .reduce((sum, sp) => sum + sp.quantity, 0);
        },

        get selectedLicenseType() {
            const select = document.querySelector('select[name="license_type_id"]');
            if (!select || !select.value) return 'Not selected';
            const option = select.options[select.selectedIndex];
            return option ? option.text : 'Not selected';
        },

        get canSubmit() {
            // Check if all selected sub-products have keys filled
            const allKeysFilled = this.subProducts.every(sp => {
                if (!sp.selected) return true;
                return sp.keys.length === sp.quantity && sp.keys.every(key => key.trim() !== '');
            });
            return this.selectedCount > 0 && this.totalKeys > 0 && allKeysFilled;
        },

        async loadUserProducts() {
            // Reset when user changes
            this.productId = '';
            this.subProducts = [];
            this.keysGenerated = false;
        },

        async loadSubProducts() {
            if (!this.productId) {
                this.subProducts = [];
                return;
            }

            try {
                const response = await fetch(`/admin/products/${this.productId}/sub-products`);
                const data = await response.json();
                
                this.subProducts = data.map(sp => ({
                    id: sp.id,
                    name: sp.name,
                    code: sp.code,
                    selected: false,
                    quantity: 1,
                    keys: []
                }));
                this.keysGenerated = false;
            } catch (error) {
                console.error('Error loading sub-products:', error);
                alert('Failed to load sub-products');
            }
        },

        updateSelection() {
            this.rebuildKeysForSelected();
        },

        increaseQty(index) {
            this.subProducts[index].quantity++;
            this.rebuildKeysForSubProduct(index);
        },

        decreaseQty(index) {
            if (this.subProducts[index].quantity > 1) {
                this.subProducts[index].quantity--;
                this.rebuildKeysForSubProduct(index);
            }
        },

        rebuildKeysForSelected() {
            this.subProducts.forEach((sp, index) => {
                if (sp.selected) {
                    this.rebuildKeysForSubProduct(index);
                }
            });
        },

        rebuildKeysForSubProduct(index) {
            const sp = this.subProducts[index];
            const currentKeys = sp.keys || [];
            
            if (sp.quantity > currentKeys.length) {
                // Add empty keys for new quantities
                for (let i = currentKeys.length; i < sp.quantity; i++) {
                    sp.keys.push('');
                }
            } else if (sp.quantity < currentKeys.length) {
                // Remove keys if quantity decreased
                sp.keys = currentKeys.slice(0, sp.quantity);
            }
        },

        generateKey() {
            // Generate hex string of desired length
            const bytes = Math.ceil(this.keyLength / 2); // 2 hex chars per byte
            const randomBytes = new Uint8Array(bytes);
            
            if (window.crypto?.getRandomValues) {
                window.crypto.getRandomValues(randomBytes);
            } else {
                for (let i = 0; i < randomBytes.length; i++) {
                    randomBytes[i] = Math.floor(Math.random() * 256);
                }
            }
            
            // Convert to hex string
            let hex = Array.from(randomBytes)
                .map(b => b.toString(16).padStart(2, '0'))
                .join('')
                .toUpperCase();
            
            // Trim to exact length
            hex = hex.substring(0, this.keyLength);
            
            // Format as XXXX-XXXX-XXXX-... (groups of 4)
            const formatted = hex.match(/.{1,4}/g);
            return formatted ? formatted.join('-') : hex;
        },

        generateAllKeys() {
            this.subProducts.forEach((sp, index) => {
                if (sp.selected && sp.quantity > 0) {
                    this.generateSubProductKeys(index);
                }
            });
        },

        generateSubProductKeys(spIndex) {
            const sp = this.subProducts[spIndex];
            for (let i = 0; i < sp.keys.length; i++) {
                sp.keys[i] = this.generateKey();
            }
        },

        generateSingleKey(spIndex, keyIndex) {
            this.subProducts[spIndex].keys[keyIndex] = this.generateKey();
        },

        async handleSubmit(e) {
            const form = e.target;
            const formData = new FormData(form);

            // Build the licenses array
            const licenses = [];
            this.subProducts.forEach(sp => {
                if (sp.selected && sp.keys.length > 0) {
                    sp.keys.forEach(key => {
                        licenses.push({
                            sub_product_id: sp.id,
                            license_key: key
                        });
                    });
                }
            });

            // Create payload
            const payload = {
                user_id: formData.get('user_id'),
                product_id: this.productId,
                license_type_id: formData.get('license_type_id'),
                max_activations: formData.get('max_activations') || null,
                expired_date: formData.get('expired_date') || null,
                licenses: licenses
            };

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const result = await response.json();

                if (!response.ok) {
                    throw new Error(result.message || 'Failed to create licenses');
                }

                window.location.href = '{{ route("admin.licenses.index") }}';
            } catch (error) {
                console.error('Error:', error);
                alert(error.message || 'Failed to generate licenses');
            }
        }
    }));
});
</script>