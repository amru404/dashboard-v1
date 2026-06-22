@csrf

@php
    $parentPickerOptions = $parentOptions->values();
    $selectedParentId = (string) old('parent_id', $product->parent_id);
@endphp

<div class="grid gap-5 sm:grid-cols-2">
    <div>
        <x-form-label for="name" value="Name" />
        <x-form-input id="name" name="name" value="{{ old('name', $product->name) }}" required class="mt-2" />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div>
        <x-form-label for="code" value="Product ID" />
        <x-form-input
            id="code"
            name="code"
            value="{{ old('code', $product->code) }}"
            maxlength="255"
            autocomplete="off"
            class="mt-2 font-mono uppercase"
            placeholder="Auto-generated from name when blank"
            :data-existing-product-ids="json_encode($existingProductIds ?? [])"
        />
        <p class="mt-2 text-xs text-madani-muted">Products can share names, but product IDs must be unique.</p>
        <x-input-error :messages="$errors->get('code')" class="mt-2" />
    </div>

    <div
        class="sm:col-span-2"
        x-data="parentProductPicker(@js($parentPickerOptions), @js($selectedParentId))"
        x-on:keydown.escape.window="close()"
    >
        <x-form-label value="Parent product" />
        <input id="parent_id" type="hidden" name="parent_id" value="{{ $selectedParentId }}" x-model="selectedId">

        <div class="mt-2 flex flex-col gap-3 rounded-xl border border-madani-border bg-madani-ghost p-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="min-w-0">
                <p class="text-sm font-semibold text-madani-deep" x-text="selectedOption() ? selectedOption().name : 'Top-level product'">
                    {{ $product->parent?->name ?? 'Top-level product' }}
                </p>
                <p class="mt-1 truncate font-mono text-xs font-semibold text-madani-muted" x-show="selectedOption()" x-text="selectedOption() ? selectedOption().code : ''">
                    {{ $product->parent?->code }}
                </p>
                <p class="mt-1 truncate text-xs text-madani-muted" x-show="selectedOption()" x-text="selectedOption() ? selectedOption().path : ''">
                    {{ $product->parent?->getCatalogPath() }}
                </p>
            </div>

            <div class="flex shrink-0 flex-wrap gap-2">
                <button type="button" class="rounded-lg border border-madani-border bg-white px-4 py-2 text-sm font-semibold text-madani-deep transition hover:border-madani-green hover:text-madani-green madani-focus" @click="open = true; $nextTick(() => $refs.parentSearch?.focus())">
                    Select parent
                </button>
                <button type="button" class="rounded-lg px-4 py-2 text-sm font-semibold text-madani-muted transition hover:bg-white hover:text-madani-deep madani-focus" x-show="selectedId" @click="clearSelection()">
                    Top-level
                </button>
            </div>
        </div>

        <x-input-error :messages="$errors->get('parent_id')" class="mt-2" />

        <div x-show="open" x-cloak class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-6">
            <div class="fixed inset-0 bg-madani-deep/50" @click="close()" aria-hidden="true"></div>

            <div class="relative mx-auto max-w-5xl overflow-hidden rounded-xl bg-white shadow-2xl">
                <div class="flex items-start justify-between gap-4 border-b border-madani-border px-5 py-4">
                    <div>
                        <h2 class="text-base font-bold text-madani-deep">Select parent product</h2>
                        <p class="mt-1 text-sm text-madani-muted" x-text="`${filteredOptions().length} products`"></p>
                    </div>
                    <button type="button" class="rounded-lg px-3 py-2 text-sm font-semibold text-madani-muted transition hover:bg-madani-ghost hover:text-madani-deep madani-focus" @click="close()">
                        Close
                    </button>
                </div>

                <div class="grid gap-3 border-b border-madani-border bg-madani-ghost px-5 py-4 sm:grid-cols-[1fr_auto]">
                    <div>
                        <x-form-label for="parent_picker_search" value="Search" />
                        <input
                            id="parent_picker_search"
                            x-ref="parentSearch"
                            type="search"
                            class="madani-input mt-2"
                            placeholder="Name, Product ID, or path"
                            x-model.debounce.150ms="search"
                        >
                    </div>

                    <div class="flex items-end">
                        <button type="button" class="w-full rounded-lg border border-madani-border bg-white px-4 py-3 text-sm font-semibold text-madani-deep transition hover:border-madani-green hover:text-madani-green madani-focus" @click="clearSelection()">
                            Top-level
                        </button>
                    </div>
                </div>

                <div class="max-h-[60vh] overflow-auto">
                    <table class="min-w-full divide-y divide-madani-border text-left text-sm">
                        <thead class="sticky top-0 bg-white text-xs font-bold uppercase tracking-[0.12em] text-madani-muted">
                            <tr>
                                <th class="px-5 py-3">Product</th>
                                <th class="px-5 py-3">Product ID</th>
                                <th class="px-5 py-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-madani-border">
                            <template x-for="option in filteredOptions()" :key="option.id">
                                <tr
                                    class="cursor-pointer transition"
                                    :class="option.id === selectedId ? 'bg-madani-pale' : 'hover:bg-madani-ghost'"
                                    @click="selectOption(option)"
                                >
                                    <td class="px-5 py-3">
                                        <div :style="indentStyle(option)">
                                            <p class="font-semibold text-madani-deep" x-text="option.name"></p>
                                            <p class="mt-1 max-w-xl truncate text-xs text-madani-muted" x-text="option.path"></p>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3 font-mono text-xs font-semibold text-madani-deep" x-text="option.code"></td>
                                    <td class="px-5 py-3 text-right">
                                        <button type="button" class="rounded-lg px-3 py-2 text-sm font-semibold text-madani-green transition hover:bg-madani-pale madani-focus" @click.stop="selectOption(option)">
                                            Select
                                        </button>
                                    </td>
                                </tr>
                            </template>

                            <tr x-show="filteredOptions().length === 0">
                                <td colspan="3" class="px-5 py-8 text-center text-sm text-madani-muted">No active products found.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-5">
    <x-form-label for="description" value="Description" />
    <textarea id="description" name="description" rows="5" class="madani-input mt-2">{{ old('description', $product->description) }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-2" />
</div>

<label for="is_active" class="mt-5 flex items-center gap-3">
    <input id="is_active" type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-madani-green shadow-sm focus:ring-madani-green" @checked(old('is_active', $product->is_active))>
    <span class="text-sm font-semibold text-madani-deep">Active product</span>
</label>

<div class="mt-8 flex flex-wrap gap-3">
    <x-button>{{ $submitLabel }}</x-button>
    <x-button variant="secondary" :href="route('admin.products.index')">Cancel</x-button>
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
