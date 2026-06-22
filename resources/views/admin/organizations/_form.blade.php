@csrf

<div class="grid gap-5 sm:grid-cols-2">
    <div>
        <x-form-label for="name" value="Name" />
        <x-form-input id="name" name="name" value="{{ old('name', $organization->name) }}" required class="mt-2" />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div>
        <x-form-label for="code" value="Code" />
        <x-form-input id="code" name="code" value="{{ old('code', $organization->code) }}" required class="mt-2 uppercase" />
        <x-input-error :messages="$errors->get('code')" class="mt-2" />
    </div>

    <div>
        <x-form-label for="email" value="Email" />
        <x-form-input id="email" name="email" type="email" value="{{ old('email', $organization->email) }}" class="mt-2" />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>

    <div>
        <x-form-label for="phone" value="Phone" />
        <x-form-input id="phone" name="phone" value="{{ old('phone', $organization->phone) }}" class="mt-2" />
        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
    </div>
</div>

<div class="mt-5">
    <x-form-label for="address" value="Address" />
    <textarea id="address" name="address" rows="4" class="madani-input mt-2">{{ old('address', $organization->address) }}</textarea>
    <x-input-error :messages="$errors->get('address')" class="mt-2" />
</div>

<label for="is_active" class="mt-5 flex items-center gap-3">
    <input id="is_active" type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-madani-green shadow-sm focus:ring-madani-green" @checked(old('is_active', $organization->is_active))>
    <span class="text-sm font-semibold text-madani-deep">Active organization</span>
</label>

<div class="mt-8 flex flex-wrap gap-3">
    <x-button>{{ $submitLabel }}</x-button>
    <x-button variant="secondary" :href="route('admin.organizations.index')">Cancel</x-button>
</div>
