@csrf

@php
    $parentPickerOptions = $parentOptions->values();
    $selectedParentId = (string) old('parent_id', $product->parent_id);
@endphp

<div class="grid gap-5 sm:grid-cols-2">
    <div>
        <x-form-label for="name" value="Name" class="text-gray-300" />
        <x-form-input 
            id="name" 
            name="name" 
            value="{{ old('name', $product->name) }}" 
            required 
            class="mt-2 bg-[#0f1829] border-[#2a3f5f] text-white placeholder-gray-500 focus:border-vd-primary focus:ring-vd-primary" />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div>
        <x-form-label for="code" value="Product ID" class="text-gray-300" />
        <x-form-input
            id="code"
            name="code"
            value="{{ old('code', $product->code) }}"
            maxlength="255"
            autocomplete="off"
            class="mt-2 font-mono uppercase bg-[#0f1829] border-[#2a3f5f] text-white placeholder-gray-500 focus:border-vd-primary focus:ring-vd-primary"
            placeholder="Auto-generated from name when blank"
            :data-existing-product-ids="json_encode($existingProductIds ?? [])"
        />
        <p class="mt-2 text-xs text-gray-400">Products can share names, but product IDs must be unique.</p>
        <x-input-error :messages="$errors->get('code')" class="mt-2" />
    </div>

    <div
        class="sm:col-span-2"
        x-data="parentProductPicker(@js($parentPickerOptions), @js($selectedParentId))"
        x-on:keydown.escape.window="close()"
    >
        <x-form-label value="Master Product" class="text-gray-300" />
        <input id="parent_id" type="hidden" name="parent_id" value="{{ $selectedParentId }}" x-model="selectedId">

        <div class="mt-2 flex flex-col gap-3 rounded-lg border border-[#2a3f5f] bg-[#0f1829] p-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold text-white" x-text="selectedOption() ? selectedOption().name : 'Master Product (no parent)'">
                    {{ $product->parent?->name ?? 'Master Product (no parent)' }}
                </p>
                <p class="mt-1 truncate font-mono text-xs font-semibold text-gray-400" x-show="selectedOption()" x-text="selectedOption() ? selectedOption().code : ''">
                    {{ $product->parent?->code }}
                </p>
                <p class="mt-1 truncate text-xs text-gray-400" x-show="selectedOption()" x-text="selectedOption() ? selectedOption().path : ''">
                    {{ $product->parent?->getCatalogPath() }}
                </p>
            </div>

            <div class="flex shrink-0 flex-wrap gap-2">
                <button 
                    type="button" 
                    class="rounded-lg border border-[#2a3f5f] bg-vd-primary/20 hover:bg-vd-primary/30 px-4 py-2 text-sm font-semibold text-vd-primary transition border-vd-primary/30 focus:ring-2 focus:ring-vd-primary/50" 
                    @click="open = true; $nextTick(() => $refs.parentSearch?.focus())">
                    Select Master Product
                </button>
                <button 
                    type="button" 
                    class="rounded-lg px-4 py-2 text-sm font-semibold text-gray-400 transition hover:bg-white/5 hover:text-white focus:ring-2 focus:ring-vd-primary/50" 
                    x-show="selectedId" 
                    @click="clearSelection()">
                    Clear (Make Master)
                </button>
            </div>
        </div>

        <p class="mt-2 text-xs text-gray-400">If no master product is selected, this will be a master product. Otherwise, it will be a sub-product.</p>
        <x-input-error :messages="$errors->get('parent_id')" class="mt-2" />

        <div x-show="open" x-cloak class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-6">
            <div class="fixed inset-0 bg-black/70" @click="close()" aria-hidden="true"></div>

            <div class="relative mx-auto max-w-5xl overflow-hidden rounded-lg bg-[#0a1420] border border-[#2a3f5f] shadow-2xl">
                <div class="flex items-start justify-between gap-4 border-b border-[#2a3f5f] px-5 py-4">
                    <div>
                        <h2 class="text-base font-bold text-white">Select Master Product</h2>
                        <p class="mt-1 text-sm text-gray-400" x-text="`${filteredOptions().length} products`"></p>
                    </div>
                    <button 
                        type="button" 
                        class="rounded-lg px-3 py-2 text-sm font-semibold text-gray-400 transition hover:bg-white/5 hover:text-white focus:ring-2 focus:ring-vd-primary/50" 
                        @click="close()">
                        Close
                    </button>
                </div>

                <div class="grid gap-3 border-b border-[#2a3f5f] bg-[#071422] px-5 py-4 sm:grid-cols-[1fr_auto]">
                    <div>
                        <x-form-label for="parent_picker_search" value="Search" class="text-gray-300" />
                        <input
                            id="parent_picker_search"
                            x-ref="parentSearch"
                            type="search"
                            class="mt-2 w-full rounded-lg border border-[#2a3f5f] bg-[#0f1829] px-4 py-2.5 text-white placeholder-gray-500 focus:border-vd-primary focus:ring-2 focus:ring-vd-primary/50 focus:outline-none transition-colors text-sm"
                            placeholder="Search by name, Product ID, or path"
                            x-model.debounce.150ms="search"
                        >
                    </div>

                    <div class="flex items-end">
                        <button 
                            type="button" 
                            class="w-full rounded-lg border border-[#2a3f5f] bg-[#0f1829] hover:bg-white/5 px-4 py-3 text-sm font-semibold text-white transition focus:ring-2 focus:ring-vd-primary/50" 
                            @click="clearSelection()">
                            Make Master Product
                        </button>
                    </div>
                </div>

                <div class="max-h-[60vh] overflow-auto">
                    <table class="min-w-full divide-y divide-[#2a3f5f] text-left text-sm">
                        <thead class="sticky top-0 bg-[#0a1420] text-xs font-bold uppercase tracking-[0.12em] text-gray-400">
                            <tr>
                                <th class="px-5 py-3">Product</th>
                                <th class="px-5 py-3">Product ID</th>
                                <th class="px-5 py-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#2a3f5f]">
                            <template x-for="option in filteredOptions()" :key="option.id">
                                <tr
                                    class="cursor-pointer transition"
                                    :class="option.id === selectedId ? 'bg-vd-primary/10' : 'hover:bg-white/5'"
                                    @click="selectOption(option)"
                                >
                                    <td class="px-5 py-3">
                                        <div :style="indentStyle(option)">
                                            <p class="font-semibold text-white" x-text="option.name"></p>
                                            <p class="mt-1 max-w-xl truncate text-xs text-gray-400" x-text="option.path"></p>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3 font-mono text-xs font-semibold text-vd-primary" x-text="option.code"></td>
                                    <td class="px-5 py-3 text-right">
                                        <button 
                                            type="button" 
                                            class="rounded-lg px-3 py-2 text-sm font-semibold text-vd-primary transition hover:bg-vd-primary/10 focus:ring-2 focus:ring-vd-primary/50" 
                                            @click.stop="selectOption(option)">
                                            Select
                                        </button>
                                    </td>
                                </tr>
                            </template>

                            <tr x-show="filteredOptions().length === 0">
                                <td colspan="3" class="px-5 py-8 text-center text-sm text-gray-400">No active products found.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-5">
    <x-form-label for="description" value="Description" class="text-gray-300" />
    <textarea 
        id="description" 
        name="description" 
        rows="5" 
        class="mt-2 w-full rounded-lg border border-[#2a3f5f] bg-[#0f1829] px-4 py-2.5 text-white placeholder-gray-500 focus:border-vd-primary focus:ring-2 focus:ring-vd-primary/50 focus:outline-none transition-colors text-sm">{{ old('description', $product->description) }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-2" />
</div>

<label for="is_active" class="mt-5 flex items-center gap-3 cursor-pointer">
    <input 
        id="is_active" 
        type="checkbox" 
        name="is_active" 
        value="1" 
        class="rounded border-[#2a3f5f] bg-[#0f1829] text-vd-primary shadow-sm focus:ring-vd-primary focus:ring-offset-0" 
        @checked(old('is_active', $product->is_active))>
    <span class="text-sm font-semibold text-white">Active product</span>
</label>

{{-- Sub-Products Section (Optional) --}}
<div class="mt-8" x-data="subProductManager()">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-lg font-bold text-white">Sub-Products <span class="text-sm font-normal text-gray-400">(Optional)</span></h3>
            <p class="text-sm text-gray-400 mt-1">Add sub-products to this master product</p>
        </div>
        <button 
            type="button"
            @click="addSubProduct()"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-vd-primary/20 hover:bg-vd-primary/30 text-vd-primary font-semibold text-sm border border-vd-primary/30 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            Add Sub-Product
        </button>
    </div>

    <div class="space-y-3">
        <template x-for="(subProduct, index) in subProducts" :key="subProduct.id">
            <div class="rounded-lg border border-[#2a3f5f] bg-[#0f1829] p-4">
                <div class="flex items-start gap-4">
                    {{-- Type Selector --}}
                    <div class="flex-shrink-0">
                        <select 
                            :name="`sub_products[${index}][type]`"
                            x-model="subProduct.type"
                            class="rounded-lg border border-[#2a3f5f] bg-[#071422] text-white px-3 py-2 text-sm focus:border-vd-primary focus:ring-2 focus:ring-vd-primary/50 focus:outline-none">
                            <option value="new">New</option>
                            <option value="existing">Import Existing</option>
                        </select>
                    </div>

                    {{-- Content Area --}}
                    <div class="flex-1 space-y-3">
                        {{-- New Sub-Product Fields --}}
                        <div x-show="subProduct.type === 'new'" class="grid gap-3 sm:grid-cols-2">
                            <div>
                                <label class="block text-xs font-medium text-gray-300 mb-1">Sub-Product Name</label>
                                <input 
                                    type="text"
                                    :name="`sub_products[${index}][name]`"
                                    x-model="subProduct.name"
                                    placeholder="Enter sub-product name"
                                    class="w-full rounded-lg border border-[#2a3f5f] bg-[#071422] text-white px-3 py-2 text-sm placeholder-gray-500 focus:border-vd-primary focus:ring-2 focus:ring-vd-primary/50 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-300 mb-1">Sub-Product ID</label>
                                <input 
                                    type="text"
                                    :name="`sub_products[${index}][code]`"
                                    x-model="subProduct.code"
                                    placeholder="Auto-generated if blank"
                                    class="w-full rounded-lg border border-[#2a3f5f] bg-[#071422] text-white px-3 py-2 text-sm font-mono uppercase placeholder-gray-500 focus:border-vd-primary focus:ring-2 focus:ring-vd-primary/50 focus:outline-none">
                            </div>
                        </div>

                        {{-- Import Existing Sub-Product --}}
                        <div x-show="subProduct.type === 'existing'">
                            <label class="block text-xs font-medium text-gray-300 mb-1">Select Existing Product</label>
                            <select 
                                :name="`sub_products[${index}][existing_id]`"
                                x-model="subProduct.existing_id"
                                @change="updateSelectedProduct(index, $event.target.value)"
                                class="w-full rounded-lg border border-[#2a3f5f] bg-[#071422] text-white px-3 py-2 text-sm focus:border-vd-primary focus:ring-2 focus:ring-vd-primary/50 focus:outline-none">
                                <option value="">-- Select a product --</option>
                                @foreach($parentPickerOptions as $option)
                                    <option value="{{ $option['id'] }}" data-name="{{ $option['name'] }}" data-code="{{ $option['code'] }}">
                                        {{ $option['name'] }} ({{ $option['code'] }})
                                    </option>
                                @endforeach
                            </select>
                            
                            {{-- Read-only display when product selected --}}
                            <div x-show="subProduct.existing_id && subProduct.selectedName" class="mt-2 p-3 rounded-lg bg-[#071422] border border-[#2a3f5f]">
                                <p class="text-sm text-white font-medium" x-text="subProduct.selectedName"></p>
                                <p class="text-xs text-gray-400 font-mono mt-1" x-text="subProduct.selectedCode"></p>
                            </div>
                        </div>
                    </div>

                    {{-- Remove Button --}}
                    <button 
                        type="button"
                        @click="removeSubProduct(index)"
                        class="flex-shrink-0 inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-500/20 hover:bg-red-500/30 text-red-400 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </template>

        {{-- Empty State --}}
        <div x-show="subProducts.length === 0" class="rounded-lg border border-dashed border-[#2a3f5f] bg-[#0f1829]/40 p-8 text-center">
            <svg class="mx-auto mb-3 h-10 w-10 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            <p class="text-sm text-gray-400">No sub-products added yet. Click "Add Sub-Product" to get started.</p>
        </div>
    </div>
</div>

<div class="mt-8 flex flex-wrap gap-3">
    <button 
        type="submit"
        class="inline-flex items-center px-6 py-2.5 rounded-lg bg-vd-primary hover:bg-vd-primary/90 text-white font-semibold text-sm transition-colors focus:ring-2 focus:ring-vd-primary/50 focus:outline-none">
        {{ $submitLabel }}
    </button>
    <a 
        href="{{ route('admin.products.index') }}"
        class="inline-flex items-center px-6 py-2.5 rounded-lg border border-[#2a3f5f] bg-white/10 hover:bg-white/15 text-white font-semibold text-sm transition-colors focus:ring-2 focus:ring-vd-primary/50 focus:outline-none">
        Cancel
    </a>
</div>

<script>
    (() => {
        const nameInput = document.getElementById('name');
        const productIdInput = document.getElementById('code');

        if (! nameInput || ! productIdInput) {
            return;
        }

        let existingProductIds = [];

        try {
            existingProductIds = JSON.parse(productIdInput.dataset.existingProductIds || '[]');
        } catch (error) {
            existingProductIds = [];
        }

        const usedProductIds = new Set(existingProductIds.map((productId) => String(productId).toUpperCase()));

        const normalizeProductId = (value) => value
            .normalize('NFKD')
            .replace(/[\u0300-\u036f]/g, '')
            .toUpperCase()
            .replace(/[^A-Z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');

        const uniqueProductId = (name) => {
            const baseProductId = normalizeProductId(name);

            if (! baseProductId) {
                return '';
            }

            let productId = baseProductId;
            let suffix = 2;

            while (usedProductIds.has(productId)) {
                productId = `${baseProductId}-${suffix}`;
                suffix += 1;
            }

            return productId;
        };

        const generateIfEmpty = () => {
            if (productIdInput.value.trim() !== '') {
                return;
            }

            const productId = uniqueProductId(nameInput.value);

            if (productId !== '') {
                productIdInput.value = productId;
            }
        };

        const normalizeManualValue = () => {
            if (productIdInput.value.trim() === '') {
                return;
            }

            productIdInput.value = normalizeProductId(productIdInput.value);
        };

        nameInput.addEventListener('blur', generateIfEmpty);
        nameInput.addEventListener('change', generateIfEmpty);
        productIdInput.addEventListener('blur', normalizeManualValue);

        generateIfEmpty();
    })();
</script>


<script>
    (() => {
        const nameInput = document.getElementById('name');
        const productIdInput = document.getElementById('code');

        if (! nameInput || ! productIdInput) {
            return;
        }

        let existingProductIds = [];

        try {
            existingProductIds = JSON.parse(productIdInput.dataset.existingProductIds || '[]');
        } catch (error) {
            existingProductIds = [];
        }

        const usedProductIds = new Set(existingProductIds.map((productId) => String(productId).toUpperCase()));

        const normalizeProductId = (value) => value
            .normalize('NFKD')
            .replace(/[\u0300-\u036f]/g, '')
            .toUpperCase()
            .replace(/[^A-Z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');

        const uniqueProductId = (name) => {
            const baseProductId = normalizeProductId(name);

            if (! baseProductId) {
                return '';
            }

            let productId = baseProductId;
            let suffix = 2;

            while (usedProductIds.has(productId)) {
                productId = `${baseProductId}-${suffix}`;
                suffix += 1;
            }

            return productId;
        };

        const generateIfEmpty = () => {
            if (productIdInput.value.trim() !== '') {
                return;
            }

            const productId = uniqueProductId(nameInput.value);

            if (productId !== '') {
                productIdInput.value = productId;
            }
        };

        const normalizeManualValue = () => {
            if (productIdInput.value.trim() === '') {
                return;
            }

            productIdInput.value = normalizeProductId(productIdInput.value);
        };

        nameInput.addEventListener('blur', generateIfEmpty);
        nameInput.addEventListener('change', generateIfEmpty);
        productIdInput.addEventListener('blur', normalizeManualValue);

        generateIfEmpty();
    })();
</script>

<script>
    // Sub-Product Manager Alpine Component
    function subProductManager() {
        return {
            subProducts: [],
            nextId: 1,

            addSubProduct() {
                this.subProducts.push({
                    id: this.nextId++,
                    type: 'new',
                    name: '',
                    code: '',
                    existing_id: '',
                    selectedName: '',
                    selectedCode: ''
                });
            },

            removeSubProduct(index) {
                this.subProducts.splice(index, 1);
            },

            updateSelectedProduct(index, productId) {
                if (!productId) {
                    this.subProducts[index].selectedName = '';
                    this.subProducts[index].selectedCode = '';
                    return;
                }

                const select = event.target;
                const option = select.options[select.selectedIndex];
                this.subProducts[index].selectedName = option.dataset.name || '';
                this.subProducts[index].selectedCode = option.dataset.code || '';
            }
        };
    }
</script>
