<x-guest-layout>
    <div class="mb-7">
        <p class="text-eyebrow tracking-[0.18em] text-vd-primary uppercase mb-3">Password Reset</p>
        <h1 class="text-headline-sm text-vd-on-surface">Set New Password</h1>
        <p class="mt-2 text-body-sm text-vd-muted leading-relaxed">
            Choose a strong new password for your account.
        </p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <x-input-label for="email" value="Email address" />
            <x-text-input id="email" type="email" name="email"
                :value="old('email', $request->email)" required autofocus
                autocomplete="username" class="mt-1" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <div>
            <x-input-label for="password" value="New Password" />
            <x-text-input id="password" type="password" name="password"
                required autocomplete="new-password"
                placeholder="••••••••" class="mt-1" />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <div>
            <x-input-label for="password_confirmation" value="Confirm Password" />
            <x-text-input id="password_confirmation" type="password" name="password_confirmation"
                required autocomplete="new-password"
                placeholder="••••••••" class="mt-1" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
        </div>

        <x-primary-button class="w-full justify-center">
            Reset Password
        </x-primary-button>
    </form>
</x-guest-layout>
