<x-guest-layout>
    @php($recaptchaEnabled = (bool) config('services.recaptcha.enabled'))

    <div class="mb-7">
        <p class="text-eyebrow tracking-[0.18em] text-vd-primary uppercase mb-3">Secure Access</p>
        <h1 class="text-headline-sm text-vd-on-surface">Sign In</h1>
        <p class="mt-2 text-body-sm text-vd-muted leading-relaxed">
            Use your Customer Area account credentials to continue.
        </p>
    </div>

    <x-auth-session-status class="mb-5 rounded-lg border border-vd-success/25 bg-vd-success/10 px-4 py-3 text-body-sm text-vd-success" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" id="login-form" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" value="Email address" />
            <x-text-input id="email" type="email" name="email"
                :value="old('email')" required autofocus autocomplete="username"
                placeholder="you@example.com" class="mt-1" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <div>
            <div class="flex items-center justify-between mb-1">
                <x-input-label for="password" value="Password" />
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                       class="text-[12px] font-semibold text-vd-primary hover:brightness-125 transition">
                        Forgot password?
                    </a>
                @endif
            </div>
            <x-text-input id="password" type="password" name="password"
                required autocomplete="current-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        @if ($recaptchaEnabled)
            <div class="rounded-lg border border-vd-border-strong bg-vd-secondary p-4">
                <div class="mb-3 flex items-center justify-between">
                    <span class="text-label-sm text-vd-on-surface">Security check</span>
                    <span class="text-eyebrow text-vd-muted">reCAPTCHA</span>
                </div>
                <div class="g-recaptcha"
                    data-sitekey="{{ config('services.recaptcha.site_key') }}"
                    data-callback="onRecaptchaVerified"
                    data-expired-callback="onRecaptchaExpired"
                    data-error-callback="onRecaptchaExpired"
                    data-theme="dark">
                </div>
                <x-input-error :messages="$errors->get('g-recaptcha-response')" class="mt-2" />
            </div>
        @endif

        <div class="flex items-center justify-between gap-4">
            <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer">
                <input id="remember_me" type="checkbox" name="remember"
                    class="h-4 w-4 rounded-sm border-vd-border-strong bg-vd-secondary text-vd-primary focus:ring-vd-primary focus:ring-offset-vd-surface">
                <span class="text-body-sm text-vd-muted">Remember me</span>
            </label>
        </div>

        <x-primary-button
            class="w-full justify-center"
            data-login-submit
            :disabled="$recaptchaEnabled">
            Sign in
        </x-primary-button>
    </form>

    @if ($recaptchaEnabled)
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        <script>
            (() => {
                const submit = document.querySelector('[data-login-submit]');
                const setEnabled = (e) => {
                    submit.disabled = !e;
                    submit.classList.toggle('opacity-50', !e);
                    submit.classList.toggle('cursor-not-allowed', !e);
                };
                setEnabled(false);
                window.onRecaptchaVerified = () => setEnabled(true);
                window.onRecaptchaExpired  = () => setEnabled(false);
            })();
        </script>
    @endif
</x-guest-layout>
