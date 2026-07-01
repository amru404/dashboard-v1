@csrf

<div class="grid gap-5 lg:grid-cols-2">
    <div>
        <x-form-label for="user_id" value="Customer user" />
        <x-form-input 
            id="user_id" 
            name="user_id" 
            type="text" 
            value="{{ $license->user->name }} - {{ $license->user->email }} - {{ $license->user->organization?->name ?? 'Unassigned' }}" 
            class="mt-2 bg-vd-border/30 cursor-not-allowed" 
            disabled 
            readonly 
        />
        <input type="hidden" name="user_id" value="{{ $license->user_id }}" />
    </div>

    <div>
        <x-form-label for="product_display" value="Product" />
        @php
            $productPath = $license->subProduct 
                ? collect([$license->product->name, $license->subProduct->name])->join(' / ')
                : $license->product->name;
            $productCode = $license->subProduct 
                ? $license->subProduct->code 
                : $license->product->code;
        @endphp
        <x-form-input 
            id="product_display" 
            name="product_display" 
            type="text" 
            value="{{ $productPath }} - {{ $productCode }}" 
            class="mt-2 bg-vd-border/30 cursor-not-allowed" 
            disabled 
            readonly 
        />
        <input type="hidden" name="product_id" value="{{ $license->product_id }}" />
        @if($license->sub_product_id)
            <input type="hidden" name="sub_product_id" value="{{ $license->sub_product_id }}" />
        @endif
    </div>

    <div>
        <x-form-label for="max_activations" value="Max Activations (optional)" />
        <x-form-input 
            id="max_activations" 
            name="max_activations" 
            type="number"
            min="1"
            placeholder="Leave blank for unlimited"
            class="mt-2"
            value="{{ old('max_activations', $license->max_activations) }}"
        />
        <x-input-error :messages="$errors->get('max_activations')" class="mt-2" />
    </div>

    <div>
        <x-form-label for="expired_date" value="Expiry Date (optional)" />
        <x-form-input 
            id="expired_date" 
            name="expired_date" 
            type="date"
            placeholder="Leave blank for no expiry"
            class="mt-2"
            value="{{ old('expired_date', $license->expired_date?->format('Y-m-d')) }}"
        />
        <x-input-error :messages="$errors->get('expired_date')" class="mt-2" />
    </div>
</div>

<div class="mt-5 rounded-xl border border-vd-border p-4">
    <x-form-label for="license_key" value="License key" />

    <div class="mt-2 flex flex-col gap-3 sm:flex-row">
        <input
            id="license_key"
            name="license_key"
            type="text"
            class="madani-input font-mono uppercase bg-vd-surface w-full rounded-xl border px-4 py-3 text-sm text-madani-deep outline-none transition focus:border-madani-green focus:ring-2 focus:ring-madani-green/15"
            autocomplete="off"
            spellcheck="false"
            placeholder="Leave blank to keep current key"
            maxlength="{{ $licenseKeyLength }}"
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
</div>

<div class="mt-8 flex flex-wrap gap-3">
    <x-button id="submit-btn">{{ $submitLabel }}</x-button>
    <x-button variant="secondary" :href="route('admin.licenses.show', $license)">Cancel</x-button>
</div>

<script>
    (() => {
        const singleGenerateBtn = document.getElementById('generate-license-key-single');
        const licenseKeyInput = document.getElementById('license_key');

        const generateFallbackKey = () => {
            const keyLength = {{ (int)$licenseKeyLength }};
            const bytes = Math.ceil(keyLength / 2);
            const randomBytes = new Uint8Array(bytes);
            
            if (window.crypto?.getRandomValues) {
                window.crypto.getRandomValues(randomBytes);
            } else {
                for (let i = 0; i < randomBytes.length; i++) {
                    randomBytes[i] = Math.floor(Math.random() * 256);
                }
            }
            
            let hex = Array.from(randomBytes)
                .map(b => b.toString(16).padStart(2, '0'))
                .join('')
                .toUpperCase();
            
            hex = hex.substring(0, keyLength);
            
            return hex.match(/.{1,4}/g).join('-');
        };

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
                if (licenseKeyInput) licenseKeyInput.value = data.license_key ?? data.license_keys?.[0] ?? '';
            } catch (e) {
                if (licenseKeyInput) licenseKeyInput.value = generateFallbackKey();
            }
        });
    })();
</script>
