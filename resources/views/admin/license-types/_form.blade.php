@csrf

<div class="grid gap-5 sm:grid-cols-2">
    <div>
        <x-form-label for="name" value="Name" />
        <x-form-input id="name" name="name" value="{{ old('name', $licenseType->name) }}" required class="mt-2" />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div>
        <x-form-label for="code" value="Code" />
        <x-form-input id="code" name="code" value="{{ old('code', $licenseType->code) }}" required class="mt-2 uppercase" />
        <x-input-error :messages="$errors->get('code')" class="mt-2" />
    </div>
</div>

<label for="is_active" class="mt-5 flex items-center gap-3">
    <input id="is_active" type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-madani-green shadow-sm focus:ring-madani-green" @checked(old('is_active', $licenseType->is_active))>
    <span class="text-sm font-semibold text-madani-deep">Active license type</span>
</label>

<div class="mt-8 flex flex-wrap gap-3">
    <x-button>{{ $submitLabel }}</x-button>
    <x-button variant="secondary" :href="route('admin.license-types.index')">Cancel</x-button>
</div>
