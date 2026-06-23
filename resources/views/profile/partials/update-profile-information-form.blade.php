<form id="send-verification" method="post" action="{{ route('verification.send') }}">@csrf</form>

<form method="post" action="{{ route('profile.update') }}" class="space-y-5">
    @csrf
    @method('patch')

    <div>
        <x-input-label for="name" value="Full Name" />
        <x-text-input id="name" name="name" type="text" class="mt-1" :value="old('name', $user->name)"
            required autofocus autocomplete="name" />
        <x-input-error class="mt-1" :messages="$errors->get('name')" />
    </div>

    <div>
        <x-input-label for="email" value="Email Address" />
        <x-text-input id="email" name="email" type="email" class="mt-1" :value="old('email', $user->email)"
            required autocomplete="username" />
        <x-input-error class="mt-1" :messages="$errors->get('email')" />

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="mt-2 rounded-lg border border-vd-warning/25 bg-vd-warning/10 px-4 py-3">
                <p class="text-body-sm text-vd-warning">
                    Your email is unverified.
                    <button form="send-verification"
                        class="underline font-semibold hover:brightness-125">
                        Re-send verification email.
                    </button>
                </p>
                @if (session('status') === 'verification-link-sent')
                    <p class="mt-1 text-body-sm text-vd-success font-medium">
                        Verification link sent!
                    </p>
                @endif
            </div>
        @endif
    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>Save Changes</x-primary-button>
        @if (session('status') === 'profile-updated')
            <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
               class="text-body-sm text-vd-success">Saved.</p>
        @endif
    </div>
</form>
