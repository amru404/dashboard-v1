<x-guest-layout>
    @php($recaptchaEnabled = (bool) config('services.recaptcha.enabled'))
    <div class="mb-8">
        <p class="text-sm font-semibold uppercase tracking-[0.18em] text-madani-green">Secure access</p>
        <h1 class="mt-3 text-3xl font-extrabold text-madani-deep">Sign in</h1>
        <p class="mt-2 text-sm leading-6 text-madani-muted">Use your Customer Area account to continue.</p>
    </div>

    <x-auth-session-status class="mb-4 rounded-lg border border-madani-success/20 bg-madani-pale px-4 py-3 text-sm text-madani-deep" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" id="login-form" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        @if ($recaptchaEnabled)
            <div class="rounded-2xl border border-madani-border bg-madani-ghost p-4">
                <div class="mb-3 flex items-center justify-between gap-3">
                    <span class="text-sm font-semibold text-madani-deep">Security check</span>
                    <span class="text-xs font-semibold text-madani-muted">reCAPTCHA</span>
                </div>

                <div
                    class="g-recaptcha"
                    data-sitekey="{{ config('services.recaptcha.site_key') }}"
                    data-callback="onRecaptchaVerified"
                    data-expired-callback="onRecaptchaExpired"
                    data-error-callback="onRecaptchaExpired"
                ></div>

                <x-input-error :messages="$errors->get('g-recaptcha-response')" class="mt-3" />
            </div>
        @endif

        <div class="flex items-center justify-between gap-4">
            <label for="remember_me" class="inline-flex items-center gap-2">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-madani-green shadow-sm focus:ring-madani-green" name="remember">
                <span class="text-sm text-madani-muted">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm font-semibold text-madani-green hover:text-madani-deep" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>

        <x-primary-button class="w-full" data-login-submit :disabled="$recaptchaEnabled">
            {{ __('Log in') }}
        </x-primary-button>
    </form>

    @if ($recaptchaEnabled)
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        <script>
            (() => {
                const submit = document.querySelector('[data-login-submit]');
                const widget = document.querySelector('.g-recaptcha');

                if (!submit || !widget) {
                    return;
                }

                const setEnabled = (enabled) => {
                    submit.disabled = !enabled;
                    submit.classList.toggle('opacity-60', !enabled);
                    submit.classList.toggle('cursor-not-allowed', !enabled);
                };

                setEnabled(false);

                window.onRecaptchaVerified = () => setEnabled(true);
                window.onRecaptchaExpired = () => setEnabled(false);
            })();
        </script>
    @endif
</x-guest-layout>
