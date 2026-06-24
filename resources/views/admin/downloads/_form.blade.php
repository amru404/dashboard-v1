@csrf

<div 
    class="grid gap-5 lg:grid-cols-2"
    x-data="{
        selectedUserId: '{{ old('user_id', $downloadItem->user_id) }}',
        selectedProductId: '{{ old('product_id', $downloadItem->product_id) }}',
        userProducts: {{ json_encode($userProducts) }},
        getProductsForUser() {
            return this.selectedUserId && this.userProducts[this.selectedUserId] ? this.userProducts[this.selectedUserId] : [];
        },
        isProductAllowed(productId) {
            if (!this.selectedUserId) return true;
            return this.getProductsForUser().includes(parseInt(productId));
        }
    }"
>
    <div>
        <x-form-label for="user_id" value="Customer" />
        <div class="relative">
            <select 
                id="user_id" 
                name="user_id" 
                x-model="selectedUserId"
                class="block w-full rounded-xl border border-vd-border bg-vd-surface px-4 py-3 pr-10 text-sm text-madani-deep outline-none transition focus:border-madani-green focus:ring-2 focus:ring-madani-green/15 mt-2"
            >
                <option value="">All entitled users</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" @selected((string) old('user_id', $downloadItem->user_id) === (string) $user->id)>
                        {{ $user->name }} - {{ $user->email }} - {{ $user->organization?->name ?? 'Unassigned' }}
                    </option>
                @endforeach
            </select>
            <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-madani-deep" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
            </svg>
        </div>
        <p class="mt-2 text-xs text-madani-muted">Choose a customer to filter available products by their entitlements.</p>
        <x-input-error :messages="$errors->get('user_id')" class="mt-2" />
    </div>

    <div>
        <x-form-label for="product_id" value="Product" />
        <div class="relative">
            <select 
                id="product_id" 
                name="product_id" 
                x-model="selectedProductId"
                class="block w-full rounded-xl border border-vd-border bg-vd-surface px-4 py-3 pr-10 text-sm text-madani-deep outline-none transition focus:border-madani-green focus:ring-2 focus:ring-madani-green/15 mt-2"
                required
            >
                <option value="">Select product</option>
                @foreach ($productOptions as $option)
                    <option 
                        value="{{ $option['id'] }}" 
                        @selected((string) old('product_id', $downloadItem->product_id) === (string) $option['id'])
                        x-show="!selectedUserId || isProductAllowed({{ $option['id'] }})"
                        :disabled="selectedUserId && !isProductAllowed({{ $option['id'] }})"
                    >
                        {{ $option['label'] }} - {{ $option['path'] }}
                    </option>
                @endforeach
            </select>
            <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-madani-deep" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
            </svg>
        </div>
        <p x-show="selectedUserId" class="mt-2 text-xs text-madani-muted">Filtered by customer's entitlements.</p>
        <x-input-error :messages="$errors->get('product_id')" class="mt-2" />
    </div>

    <div>
        <x-form-label for="file_name" value="Display file name" />
        <x-form-input id="file_name" name="file_name" value="{{ old('file_name', $downloadItem->file_name) }}" placeholder="Uses uploaded name when blank" class="mt-2" />
        <x-input-error :messages="$errors->get('file_name')" class="mt-2" />
    </div>

    <div>
        <x-form-label for="version" value="Version" />
        <x-form-input id="version" name="version" value="{{ old('version', $downloadItem->version) }}" class="mt-2" />
        <x-input-error :messages="$errors->get('version')" class="mt-2" />
    </div>

    <div class="rounded-xl border border-madani-border bg-madani-ghost p-4 lg:col-span-2">
        <x-form-label for="file_upload" value="Upload private file" />
        <input id="file_upload" name="file_upload" type="file" class="block w-full rounded-xl border border-vd-border bg-vd-surface px-4 py-3 text-sm text-madani-deep outline-none transition focus:border-madani-green focus:ring-2 focus:ring-madani-green/15 mt-2">
        <p class="mt-2 text-xs text-madani-muted">Uploaded files are stored under storage/app/private/downloads.</p>
        <x-input-error :messages="$errors->get('file_upload')" class="mt-2" />
    </div>

    <div>
        <x-form-label for="file_path" value="Registered private path" />
        <x-form-input id="file_path" name="file_path" value="{{ old('file_path', $downloadItem->file_path) }}" placeholder="downloads/example-installer.exe" class="mt-2 font-mono" />
        <p class="mt-2 text-xs text-madani-muted">Use a relative path under downloads/ when registering an existing private file.</p>
        <x-input-error :messages="$errors->get('file_path')" class="mt-2" />
    </div>

    <div>
        <x-form-label for="file_size" value="File size in bytes" />
        <x-form-input id="file_size" name="file_size" type="number" min="0" value="{{ old('file_size', $downloadItem->file_size) }}" placeholder="Filled automatically for uploads" class="mt-2" />
        <x-input-error :messages="$errors->get('file_size')" class="mt-2" />
    </div>

    <div>
        <x-form-label for="expired_date" value="Download item expiry" />
        <x-form-input id="expired_date" name="expired_date" type="date" value="{{ old('expired_date', $downloadItem->expired_date?->format('Y-m-d')) }}" class="mt-2" />
        <x-input-error :messages="$errors->get('expired_date')" class="mt-2" />
    </div>

    <div class="flex items-end">
        <label for="is_active" class="flex items-center gap-3">
            <input id="is_active" type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-madani-green shadow-sm focus:ring-madani-green" @checked(old('is_active', $downloadItem->is_active))>
            <span class="text-sm font-semibold text-madani-deep">Active download item</span>
        </label>
    </div>
</div>

<div class="mt-6 rounded-xl border border-madani-border bg-madani-ghost p-4">
    <p class="text-sm font-semibold text-madani-deep">Access rule preview</p>
    <p class="mt-2 text-sm leading-6 text-madani-muted">
        Day 14 delivery will allow this file only when the item is active, not expired, the customer has an active entitlement for the product, the entitlement download window is open, and the item is either public to entitled users or assigned to that customer.
    </p>
</div>

<div class="mt-8 flex flex-wrap gap-3">
    <x-button>{{ $submitLabel }}</x-button>
    <x-button variant="secondary" :href="route('admin.download-items.index')">Cancel</x-button>
</div>
