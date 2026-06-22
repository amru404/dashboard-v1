@csrf

@php
    $isEditing = $license->exists;
@endphp

<div class="grid gap-5 lg:grid-cols-2">
    <div>
        <x-form-label for="user_id" value="Customer user" />
        <select id="user_id" name="user_id" class="madani-input mt-2" required>
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
        <select id="license_type_id" name="license_type_id" class="madani-input mt-2" required>
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
        <select id="product_id" name="product_id" class="madani-input mt-2" required>
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
        <select id="sub_product_id" name="sub_product_id" class="madani-input mt-2">
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
        <x-form-label for="max_activations" value="Max activations" />
        <x-form-input id="max_activations" name="max_activations" type="number" min="1" value="{{ old('max_activations', $license->max_activations) }}" placeholder="Unlimited when blank" class="mt-2" />
        <x-input-error :messages="$errors->get('max_activations')" class="mt-2" />
    </div>

    <div>
        <x-form-label for="expired_date" value="Expiry date" />
        <x-form-input id="expired_date" name="expired_date" type="date" value="{{ old('expired_date', $license->expired_date?->format('Y-m-d')) }}" class="mt-2" />
        <x-input-error :messages="$errors->get('expired_date')" class="mt-2" />
    </div>

    <div class="rounded-xl border border-madani-border bg-madani-ghost p-4">
        <x-form-label for="license_key" :value="$isEditing ? 'New license key' : 'License key'" />
        <div class="mt-2 flex flex-col gap-3 sm:flex-row">
            <input
                id="license_key"
                name="license_key"
                type="text"
                class="madani-input font-mono uppercase"
                autocomplete="off"
                spellcheck="false"
                placeholder="{{ $isEditing ? 'Leave blank to keep current key' : 'Generate or enter a key' }}"
                @if (! $isEditing) required @endif
            >
            <button
                id="generate-license-key"
                type="button"
                data-url="{{ route('admin.licenses.generate-key') }}"
                data-token="{{ csrf_token() }}"
                class="inline-flex items-center justify-center rounded-lg border border-madani-deep px-4 py-2 text-sm font-semibold text-madani-deep transition hover:bg-madani-deep hover:text-white"
            >
                Generate Key
            </button>
        </div>
        @if ($isEditing)
            <p class="mt-2 font-mono text-xs font-semibold text-madani-muted">Current: {{ $license->masked_license_key }}</p>
        @endif
        <p class="mt-2 text-xs text-madani-muted">Plaintext is encrypted on save. Use reveal only when support needs the original key.</p>
        <x-input-error :messages="$errors->get('license_key')" class="mt-2" />
    </div>
</div>

<div class="mt-8 flex flex-wrap gap-3">
    <x-button>{{ $submitLabel }}</x-button>
    <x-button variant="secondary" :href="route('admin.licenses.index')">Cancel</x-button>
</div>

<script>
    (() => {
        const input = document.getElementById('license_key');
        const button = document.getElementById('generate-license-key');

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

        button?.addEventListener('click', async () => {
            try {
                const response = await fetch(button.dataset.url, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': button.dataset.token,
                    },
                });
                const data = await response.json();

                if (! response.ok) {
                    throw new Error(data.message || 'Unable to generate key.');
                }

                input.value = data.license_key;
            } catch (error) {
                input.value = generateFallbackKey();
            }

            input.focus();
        });
    })();
</script>
