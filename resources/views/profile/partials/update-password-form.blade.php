<form method="post" action="{{ route('password.update') }}" class="space-y-5">
    @csrf
    @method('put')

    <div>
        <x-input-label for="update_password_current_password" value="Current Password" />
        <x-text-input id="update_password_current_password" name="current_password"
            type="password" class="mt-1" autocomplete="current-password"
            placeholder="••••••••" />
        <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="update_password_password" value="New Password" />
        <x-text-input id="update_password_password" name="password"
            type="password" class="mt-1" autocomplete="new-password"
            placeholder="••••••••" />
        <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="update_password_password_confirmation" value="Confirm New Password" />
        <x-text-input id="update_password_password_confirmation" name="password_confirmation"
            type="password" class="mt-1" autocomplete="new-password"
            placeholder="••••••••" />
        <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1" />
    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>Update Password</x-primary-button>
        @if (session('status') === 'password-updated')
            <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
               class="text-body-sm text-vd-success">Saved.</p>
        @endif
    </div>
</form>
