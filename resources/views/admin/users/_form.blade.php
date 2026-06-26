@csrf

@php
    $isEditingSelf = $user->exists && $user->is(auth()->user());
@endphp

<div class="grid gap-5 sm:grid-cols-2">
    <div>
        <label for="name" class="block text-sm font-semibold text-white mb-2">Name</label>
        <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required class="w-full px-4 py-2.5 bg-[#0f1829] border border-[#2a3f5f] rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-vd-primary focus:ring-1 focus:ring-vd-primary/20" />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div>
        <label for="email" class="block text-sm font-semibold text-white mb-2">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required class="w-full px-4 py-2.5 bg-[#0f1829] border border-[#2a3f5f] rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-vd-primary focus:ring-1 focus:ring-vd-primary/20" />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>

    <div>
        <label for="organization_id" class="block text-sm font-semibold text-white mb-2">Organization</label>
        <select id="organization_id" name="organization_id" class="w-full px-4 py-2.5 bg-[#0f1829] border border-[#2a3f5f] rounded-lg text-white focus:outline-none focus:border-vd-primary focus:ring-1 focus:ring-vd-primary/20">
            <option value="">Unassigned</option>
            @foreach ($organizations as $organization)
                <option value="{{ $organization->id }}" @selected((string) old('organization_id', $user->organization_id) === (string) $organization->id)>
                    {{ $organization->name }} ({{ $organization->code }})
                </option>
            @endforeach
        </select>
        <p class="mt-2 text-xs text-gray-400">Required for customer users. Admin users may use the system organization.</p>
        <x-input-error :messages="$errors->get('organization_id')" class="mt-2" />
    </div>

    <div>
        <label for="role" class="block text-sm font-semibold text-white mb-2">Role</label>
        <select id="role" name="role" required class="w-full px-4 py-2.5 bg-[#0f1829] border border-[#2a3f5f] rounded-lg text-white focus:outline-none focus:border-vd-primary focus:ring-1 focus:ring-vd-primary/20">
            @foreach ($roles as $role)
                <option value="{{ $role }}" @selected(old('role', $user->role) === $role)>{{ ucfirst($role) }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('role')" class="mt-2" />
    </div>

    <div class="sm:col-span-2 rounded-lg border border-[#2a3f5f] bg-[#0f1829]/50 p-4">
        <label for="password" class="block text-sm font-semibold text-white mb-2">{{ $user->exists ? 'New password' : 'Temporary password' }}</label>
        <input
            id="password"
            name="password"
            type="password"
            class="w-full px-4 py-2.5 bg-[#0f1829] border border-[#2a3f5f] rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-vd-primary focus:ring-1 focus:ring-vd-primary/20"
            autocomplete="new-password"
            placeholder="{{ $user->exists ? 'Leave blank to keep current password' : 'Set a temporary password' }}"
            @if (! $user->exists) required @endif
        >
        <p class="mt-2 text-xs text-gray-400">
            {{ $user->exists ? 'Only enter a value if you want to change this user password.' : 'Required for new users. Minimum 8 characters.' }}
        </p>
        <x-input-error :messages="$errors->get('password')" class="mt-2" />
    </div>
</div>

@if ($isEditingSelf)
    <input type="hidden" name="is_active" value="1">
@endif

<label for="is_active" class="mt-5 flex items-center gap-3">
    <input
        id="is_active"
        type="checkbox"
        name="is_active"
        value="1"
        class="rounded border-gray-600 bg-[#0f1829] text-vd-primary shadow-sm focus:ring-vd-primary/20"
        @checked(old('is_active', $user->is_active))
        @disabled($isEditingSelf)
    >
    <span class="text-sm font-semibold text-white">Active account</span>
</label>

@if ($isEditingSelf)
    <p class="mt-2 text-sm text-gray-400">Your own account will remain active to prevent lockout.</p>
@endif

<div class="mt-8 flex flex-wrap gap-3">
    <button type="submit" class="inline-flex items-center px-4 py-2.5 rounded-lg bg-vd-primary hover:bg-vd-primary/90 text-white font-semibold text-sm transition-colors">
        {{ $submitLabel }}
    </button>
    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2.5 rounded-lg text-gray-300 hover:text-white font-semibold text-sm transition-colors">
        Cancel
    </a>
</div>
