@csrf

<div class="grid gap-5 lg:grid-cols-2">
    <div>
        <x-form-label for="user_id" value="Customer user" />
        <div class="relative mt-2">
            <select id="user_id" name="user_id" class="block w-full rounded-xl border vd-input px-4 py-3 pr-10 text-sm text-madani-deep outline-none transition focus:border-madani-green focus:ring-2 focus:ring-madani-green/15" required>
                <option value="">Select customer</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" @selected((string) old('user_id', $entitlement->user_id) === (string) $user->id)>
                        {{ $user->name }} - {{ $user->email }} - {{ $user->organization?->name ?? 'Unassigned' }}
                    </option>
                @endforeach
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-madani-muted">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M6 8l4 4 4-4" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
        </div>
        <x-input-error :messages="$errors->get('user_id')" class="mt-2" />
    </div>

    <div>
        <x-form-label for="product_id" value="Product" />
        <div class="relative mt-2">
            <select id="product_id" name="product_id" class="block w-full rounded-xl border vd-input px-4 py-3 pr-10 text-sm text-madani-deep outline-none transition focus:border-madani-green focus:ring-2 focus:ring-madani-green/15" required>
                <option value="">Select product</option>
                 @foreach ($productOptions as $option)
                    <option value="{{ $option['id'] }}" @selected((string) old('product_id', $entitlement->product_id) === (string) $option['id'])>
                        {{ $option['label'] }} - {{ $option['code'] }}
                    </option>
                @endforeach
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-madani-muted">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M6 8l4 4 4-4" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
        </div>
        <p class="mt-2 text-xs text-madani-muted">One entitlement is allowed per user/product pair.</p>
        <x-input-error :messages="$errors->get('product_id')" class="mt-2" />
    </div>

    <div>
        <x-form-label for="start_date" value="Access start" />
        <x-form-input id="start_date" name="start_date" type="date" value="{{ old('start_date', $entitlement->start_date?->format('Y-m-d') ?? now()->toDateString()) }}" required class="mt-2 color-scheme-dark" />
        <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
    </div>

    <div>
        <x-form-label for="end_date" value="Access end" />
        <x-form-input id="end_date" name="end_date" type="date" value="{{ old('end_date', $entitlement->end_date?->format('Y-m-d')) }}" class="mt-2 color-scheme-dark" />
        <p class="mt-2 text-xs text-madani-muted">Leave blank for open-ended product access.</p>
        <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
    </div>

    <div>
        <x-form-label for="download_expired_date" value="Download access end" />
        <x-form-input id="download_expired_date" name="download_expired_date" type="date" value="{{ old('download_expired_date', $entitlement->download_expired_date?->format('Y-m-d')) }}" class="mt-2 color-scheme-dark" />
        <p class="mt-2 text-xs text-madani-muted">Leave blank to allow downloads while the entitlement is current.</p>
        <x-input-error :messages="$errors->get('download_expired_date')" class="mt-2" />
    </div>

    <div>
        <x-form-label for="status" value="Status" />
        <div class="relative mt-2">
            <select id="status" name="status" class="block w-full rounded-xl border vd-input px-4 py-3 pr-10 text-sm text-madani-deep outline-none transition focus:border-madani-green focus:ring-2 focus:ring-madani-green/15" required>
                @foreach ($statuses as $status)
                    <option value="{{ $status }}" @selected(old('status', $entitlement->status ?? 'active') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-madani-muted">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M6 8l4 4 4-4" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
        </div>
        <x-input-error :messages="$errors->get('status')" class="mt-2" />
    </div>
</div>

<div class="mt-6 rounded-xl border vd-input p-4">
    <p class="text-sm font-semibold text-madani-deep">Access note</p>
    <p class="mt-2 text-sm leading-6 text-madani-muted">
        This grant controls customer portal product and download access. It does not activate installer devices and does not replace license records.
    </p>
</div>

<div class="mt-8 flex flex-wrap gap-3">
    <x-button>{{ $submitLabel }}</x-button>
    <x-button variant="secondary" :href="route('admin.entitlements.index')">Cancel</x-button>
</div>
