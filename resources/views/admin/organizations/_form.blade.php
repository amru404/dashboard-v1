@csrf

<div class="grid gap-5 sm:grid-cols-2">
    <div>
        <label for="name" class="block text-sm font-semibold text-white mb-2">Name</label>
        <input id="name" name="name" type="text" value="{{ old('name', $organization->name) }}" required class="w-full px-4 py-2.5 bg-[#0f1829] border border-[#2a3f5f] rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-vd-primary focus:ring-1 focus:ring-vd-primary/20" />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div>
        <label for="code" class="block text-sm font-semibold text-white mb-2">Code</label>
        <input id="code" name="code" type="text" value="{{ old('code', $organization->code) }}" required class="w-full px-4 py-2.5 bg-[#0f1829] border border-[#2a3f5f] rounded-lg text-white placeholder-gray-500 uppercase focus:outline-none focus:border-vd-primary focus:ring-1 focus:ring-vd-primary/20" />
        <x-input-error :messages="$errors->get('code')" class="mt-2" />
    </div>

    <div>
        <label for="email" class="block text-sm font-semibold text-white mb-2">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email', $organization->email) }}" class="w-full px-4 py-2.5 bg-[#0f1829] border border-[#2a3f5f] rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-vd-primary focus:ring-1 focus:ring-vd-primary/20" />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>

    <div>
        <label for="phone" class="block text-sm font-semibold text-white mb-2">Phone</label>
        <input id="phone" name="phone" type="text" value="{{ old('phone', $organization->phone) }}" class="w-full px-4 py-2.5 bg-[#0f1829] border border-[#2a3f5f] rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-vd-primary focus:ring-1 focus:ring-vd-primary/20" />
        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
    </div>
</div>

<div class="mt-5">
    <label for="address" class="block text-sm font-semibold text-white mb-2">Address</label>
    <textarea id="address" name="address" rows="4" class="w-full px-4 py-2.5 bg-[#0f1829] border border-[#2a3f5f] rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-vd-primary focus:ring-1 focus:ring-vd-primary/20">{{ old('address', $organization->address) }}</textarea>
    <x-input-error :messages="$errors->get('address')" class="mt-2" />
</div>

<label for="is_active" class="mt-5 flex items-center gap-3">
    <input id="is_active" type="checkbox" name="is_active" value="1" class="rounded border-gray-600 bg-[#0f1829] text-vd-primary shadow-sm focus:ring-vd-primary/20" @checked(old('is_active', $organization->is_active))>
    <span class="text-sm font-semibold text-white">Active organization</span>
</label>

<div class="mt-8 flex flex-wrap gap-3">
    <button type="submit" class="inline-flex items-center px-4 py-2.5 rounded-lg bg-vd-primary hover:bg-vd-primary/90 text-white font-semibold text-sm transition-colors">
        {{ $submitLabel }}
    </button>
    <a href="{{ route('admin.organizations.index') }}" class="inline-flex items-center px-4 py-2.5 rounded-lg text-gray-300 hover:text-white font-semibold text-sm transition-colors">
        Cancel
    </a>
</div>
