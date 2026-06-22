@csrf

@php
    $isEditingSelf = $user->exists && $user->is(auth()->user());
@endphp

<div class="grid gap-5 sm:grid-cols-2">
    <div>
        <x-form-label for="name" value="Name" />
        <x-form-input id="name" name="name" value="{{ old('name', $user->name) }}" required class="mt-2" />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div>
        <x-form-label for="email" value="Email" />
        <x-form-input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required class="mt-2" />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>

    <div>
        <x-form-label for="organization_id" value="Organization" />
        <select id="organization_id" name="organization_id" class="madani-input mt-2">
            <option value="">Unassigned</option>
            @foreach ($organizations as $organization)
                <option value="{{ $organization->id }}" @selected((string) old('organization_id', $user->organization_id) === (string) $organization->id)>
                    {{ $organization->name }} ({{ $organization->code }})
                </option>
            @endforeach
        </select>
        <p class="mt-2 text-xs text-madani-muted">Required for customer users. Admin users may use the system organization.</p>
        <x-input-error :messages="$errors->get('organization_id')" class="mt-2" />
    </div>

    <div>
        <x-form-label for="role" value="Role" />
        <select id="role" name="role" class="madani-input mt-2" required>
            @foreach ($roles as $role)
                <option value="{{ $role }}" @selected(old('role', $user->role) === $role)>{{ ucfirst($role) }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('role')" class="mt-2" />
    </div>

    <div class="rounded-xl border border-madani-border bg-madani-ghost p-4 sm:col-span-2">
        <x-form-label for="password" :value="$user->exists ? 'New password' : 'Temporary password'" />
        <input
            id="password"
            name="password"
            type="password"
            class="madani-input mt-2"
            autocomplete="new-password"
            placeholder="{{ $user->exists ? 'Leave blank to keep current password' : 'Set a temporary password' }}"
            @if (! $user->exists) required @endif
        >
        <p class="mt-2 text-xs text-madani-muted">
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
        class="rounded border-gray-300 text-madani-green shadow-sm focus:ring-madani-green"
        @checked(old('is_active', $user->is_active))
        @disabled($isEditingSelf)
    >
    <span class="text-sm font-semibold text-madani-deep">Active account</span>
</label>

@if ($isEditingSelf)
    <p class="mt-2 text-sm text-madani-muted">Your own account will remain active to prevent lockout.</p>
@endif

<div class="mt-8 flex flex-wrap gap-3">
    <x-button>{{ $submitLabel }}</x-button>
    <x-button variant="secondary" :href="route('admin.users.index')">Cancel</x-button>
</div>
