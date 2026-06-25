@csrf

@php
    $isEditing = $license->exists;
@endphp

<div class="grid gap-5 lg:grid-cols-2">
    <div>
        <x-form-label for="user_id" value="Customer user" />
        <select id="user_id" name="user_id" class="madani-input mt-2 lock w-full rounded-xl border bg-vd-surface px-4 py-3 pr-10 text-sm text-madani-deep outline-none transition focus:border-madani-green focus:ring-2 focus:ring-madani-green/15" required>
            <option value="">Select customer</option>
            @foreach ($users as $user)
                <option value="{{ $user->id }}" @selected((string) old('user_id', $license->user_id) === (string) $user->id)>
                    {{ $user->name }} - {{ $user->email }} - {{ $user->organization?->name ?? 'Unassigned' }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('user_id')" class="mt-2" />
    </div>

    <div>
        <x-form-label for="license_type_id" value="License type" />
        <select id="license_type_id" name="license_type_id" class="madani-input mt-2 lock w-full rounded-xl border bg-vd-surface px-4 py-3 pr-10 text-sm text-madani-deep outline-none transition focus:border-madani-green focus:ring-2 focus:ring-madani-green/15" required>
            <option value="">Select type</option>
            @foreach ($licenseTypes as $licenseType)
                <option value="{{ $licenseType->id }}" @selected((string) old('license_type_id', $license->license_type_id) === (string) $licenseType->id)>
                    {{ $licenseType->name }} ({{ $licenseType->code }}){{ $licenseType->is_active ? '' : ' - inactive' }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('license_type_id')" class="mt-2" />
    </div>

    <div>
        <x-form-label for="product_id" value="Product" />
        <select id="product_id" name="product_id" class="madani-input mt-2 lock w-full rounded-xl border bg-vd-surface px-4 py-3 pr-10 text-sm text-madani-deep outline-none transition focus:border-madani-green focus:ring-2 focus:ring-madani-green/15" required>
            <option value="">Select product</option>
            @foreach ($productOptions as $option)
                <option value="{{ $option['id'] }}" @selected((string) old('product_id', $license->product_id) === (string) $option['id'])>
                    {{ $option['label'] }} - {{ $option['path'] }}
                </option>
            @endforeach
        </select>
        <p class="mt-2 text-xs text-madani-muted">Indented by recursive product hierarchy.</p>
        <x-input-error :messages="$errors->get('product_id')" class="mt-2" />
    </div>

    <div>
        <x-form-label for="sub_product_id" value="Sub-product" />
        <select id="sub_product_id" name="sub_product_id" class="madani-input mt-2 lock w-full rounded-xl border bg-vd-surface px-4 py-3 pr-10 text-sm text-madani-deep outline-none transition focus:border-madani-green focus:ring-2 focus:ring-madani-green/15">
            <option value="">No sub-product</option>
            @foreach ($productOptions as $option)
                <option value="{{ $option['id'] }}" @selected((string) old('sub_product_id', $license->sub_product_id) === (string) $option['id'])>
                    {{ $option['label'] }} - {{ $option['path'] }}
                </option>
            @endforeach
        </select>
        <p class="mt-2 text-xs text-madani-muted">Uses the same products table. Choose a child or descendant of the main product.</p>
        <x-input-error :messages="$errors->get('sub_product_id')" class="mt-2" />
    </div>

    <div>
        <x-form-label for="quantity" value="Quantity" />
        <x-form-input id="quantity" name="quantity" type="number" min="1" value="{{ old('quantity', $license->quantity ?? 1) }}" required class="mt-2" />
        <x-input-error :messages="$errors->get('quantity')" class="mt-2" />
    </div>

     <div>
        <x-form-label for="max_activations" value="Max Device activations" />
        <x-form-input id="max_activations" name="max_activations" type="number" min="1" value="{{ old('max_activations', $license->max_activations) }}" placeholder="Unlimited when blank" class="mt-2" />
        <x-input-error :messages="$errors->get('max_activations')" class="mt-2" />
    </div>


    <div class="rounded-xl border border-vd-border p-4">
        <x-form-label for="license_key" :value="$isEditing ? 'New license key' : 'License key(s)'" />

        @if ($isEditing)
            <div class="mt-2 flex flex-col gap-3 sm:flex-row">
                <input
                    id="license_key"
                    name="license_key"
                    type="text"
                    class="madani-input font-mono uppercase bg-vd-surface w-full rounded-xl border px-4 py-3 text-sm text-madani-deep outline-none transition focus:border-madani-green focus:ring-2 focus:ring-madani-green/15"
                    autocomplete="off"
                    spellcheck="false"
                    placeholder="Leave blank to keep current key"
                >
                <button
                    id="generate-license-key-single"
                    type="button"
                    data-url="{{ route('admin.licenses.generate-key') }}"
                    data-token="{{ csrf_token() }}"
                    class="inline-flex items-center justify-center rounded-lg border border-madani-deep px-4 py-2 text-sm font-semibold text-madani-deep transition hover:bg-madani-deep hover:text-white"
                >
                    Generate Key
                </button>
            </div>
            <p class="mt-2 font-mono text-xs font-semibold text-madani-muted">Current: {{ $license->masked_license_key }}</p>
            <p class="mt-2 text-xs text-madani-muted">Plaintext is encrypted on save. Use reveal only when support needs the original key.</p>
            <x-input-error :messages="$errors->get('license_key')" class="mt-2" />
        @else
            <div class="mt-2">
                <div id="license-keys-container" class="space-y-3"></div>
                <template id="license-key-template">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                        <input
                            type="text"
                            name="license_keys[]"
                            class="madani-input font-mono uppercase bg-vd-surface w-full rounded-xl border px-4 py-3 text-sm text-madani-deep outline-none transition focus:border-madani-green focus:ring-2 focus:ring-madani-green/15"
                            autocomplete="off"
                            spellcheck="false"
                            placeholder="Generate or enter a key"
                        />
                        <button type="button" class="generate-license-btn inline-flex items-center justify-center rounded-lg border border-madani-deep px-4 py-2 text-sm font-semibold text-madani-deep transition hover:bg-madani-deep hover:text-white">
                            Generate
                        </button>
                    </div>
                </template>
                <div id="generated-keys-container" class="mt-3"></div>
                <p class="mt-2 text-xs text-madani-muted">Plaintext is encrypted on save. Each generated key will be submitted as part of the batch.</p>
                <x-input-error :messages="$errors->get('license_keys')" class="mt-2" />
            </div>
        @endif
    </div>

    <div>
        <x-form-label for="expired_date" value="Expiry date" />
        <x-form-input id="expired_date" name="expired_date" type="date" value="{{ old('expired_date', $license->expired_date?->format('Y-m-d')) }}" class="mt-2" />
        <x-input-error :messages="$errors->get('expired_date')" class="mt-2" />
    </div>

</div>

<div class="mt-8 flex flex-wrap gap-3">
    <x-button>{{ $submitLabel }}</x-button>
    <x-button variant="secondary" :href="route('admin.licenses.index')">Cancel</x-button>
</div>

<script>
    (() => {
        const form = document.querySelector('form');
        const generatedContainer = document.getElementById('generated-keys-container');
        const licenseKeysContainer = document.getElementById('license-keys-container');
        const template = document.getElementById('license-key-template');
        const singleGenerateBtn = document.getElementById('generate-license-key-single');

        const generateLocalGroup = () => {
            const bytes = new Uint8Array(2);

            if (window.crypto?.getRandomValues) {
                window.crypto.getRandomValues(bytes);
            } else {
                bytes[0] = Math.floor(Math.random() * 256);
                bytes[1] = Math.floor(Math.random() * 256);
            }

            return Array.from(bytes)
                .map((byte) => byte.toString(16).padStart(2, '0'))
                .join('')
                .toUpperCase();
        };

        const generateFallbackKey = () => Array.from({ length: 4 }, generateLocalGroup).join('-');

        const clearGeneratedArea = () => {
            generatedContainer.innerHTML = '';
        };

        const renderGeneratedKeys = (keys) => {
            clearGeneratedArea();
            if (!Array.isArray(keys) || keys.length === 0) return;

            // if dynamic fields exist, fill them and append hidden inputs
            const provided = document.querySelectorAll('input[name="license_keys[]"]');
            if (provided.length) {
                keys.forEach((k, i) => {
                    if (provided[i]) {
                        provided[i].value = k;
                    }
                });

                // also show readonly textarea for copy
                const textarea = document.createElement('textarea');
                textarea.className = 'w-full mt-2 rounded-lg bg-vd-surface border border-vd-border p-3 text-sm font-mono text-vd-on-surface';
                textarea.readOnly = true;
                textarea.rows = Math.min(8, keys.length);
                textarea.textContent = keys.join('\n');
                generatedContainer.appendChild(textarea);
            }
        };

        // Attach single generate for edit mode
        singleGenerateBtn?.addEventListener('click', async () => {
            const btn = singleGenerateBtn;
            try {
                const response = await fetch(btn.dataset.url, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': btn.dataset.token,
                    },
                    body: JSON.stringify({ quantity: 1 }),
                });
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Unable to generate key');
                const input = document.getElementById('license_key');
                if (input) input.value = data.license_key ?? data.license_keys?.[0] ?? '';
            } catch (e) {
                const input = document.getElementById('license_key');
                if (input) input.value = generateFallbackKey();
            }
        });

        // dynamic generation per-row
        const attachPerRowHandlers = (row) => {
            const genBtn = row.querySelector('.generate-license-btn');
            const input = row.querySelector('input[name="license_keys[]"]');
            if (!genBtn || !input) return;

            genBtn.addEventListener('click', async () => {
                try {
                    const resp = await fetch('{{ route('admin.licenses.generate-key') }}', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({ quantity: 1 }),
                    });
                    const data = await resp.json();
                    if (!resp.ok) throw new Error(data.message || 'Unable to generate key');
                    input.value = data.license_key ?? (data.license_keys ? data.license_keys[0] : '');
                } catch (e) {
                    input.value = generateFallbackKey();
                }
            });
        };

        const rebuildLicenseFields = () => {
            if (!licenseKeysContainer || !template) return;
            const quantityInput = document.getElementById('quantity');
            const quantity = Math.max(1, Number(quantityInput?.value || 1));

            // clear existing
            licenseKeysContainer.innerHTML = '';

            for (let i = 0; i < quantity; i++) {
                const node = template.content.cloneNode(true);
                const wrapper = node.querySelector ? node.querySelector('div') : node.children[0];
                licenseKeysContainer.appendChild(node);
                const appended = licenseKeysContainer.lastElementChild;
                // attach handler
                attachPerRowHandlers(appended);
            }
        };

        // initialize on load
        rebuildLicenseFields();

        // listen to quantity changes
        const qtyEl = document.getElementById('quantity');
        qtyEl?.addEventListener('input', () => rebuildLicenseFields());

        // Intercept form submit when the current form is creating license keys via license_keys[]
        form?.addEventListener('submit', async (e) => {
            const quantityInput = document.getElementById('quantity');
            const quantity = Number(quantityInput?.value || 1) || 1;
            const licenseKeyInputs = document.querySelectorAll('input[name="license_keys[]"]');

            if (!licenseKeyInputs.length) return; // no batch-style license key inputs, allow normal submit

            e.preventDefault();

            const batchUrl = form.dataset.batchUrl;
            const csrfToken = form.dataset.token;

            if (!batchUrl || !csrfToken) {
                form.submit();
                return;
            }

            // build payload
            const payload = {
                user_id: document.getElementById('user_id')?.value,
                license_type_id: document.getElementById('license_type_id')?.value,
                product_id: document.getElementById('product_id')?.value,
                sub_product_id: document.getElementById('sub_product_id')?.value || null,
                quantity: document.getElementById('quantity')?.value,
                max_activations: document.getElementById('max_activations')?.value || null,
                expired_date: document.getElementById('expired_date')?.value || null,
                license_count: Number(quantity),
            };

            // collect license_keys[] if present
            const provided = Array.from(licenseKeyInputs).map(i => i.value).filter(Boolean);
            if (provided.length) payload.license_keys = provided;

            try {
                const resp = await fetch(batchUrl, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify(payload),
                });

                const json = await resp.json();

                if (!resp.ok) {
                    throw new Error(json.message || 'Batch create failed');
                }

                // redirect if provided
                if (json.redirect) {
                    window.location.href = json.redirect;
                    return;
                }

                // otherwise reload
                window.location.reload();
            } catch (error) {
                alert(error.message || 'Unable to create licenses.');
            }
        });
    })();
</script>
