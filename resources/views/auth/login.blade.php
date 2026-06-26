<x-guest-layout>
    @php($recaptchaEnabled = (bool) config('services.recaptcha.enabled'))

    {{-- ── Welcome section ── --}}
    <div class="mb-8">
        <h1 class="text-headline-md text-vd-on-surface mb-2">Welcome Back</h1>
        <p class="text-body-sm text-vd-muted leading-relaxed">
            Sign in to your Customer Area account to continue.
        </p>
    </div>

    {{-- ── Status message ── --}}
    <x-auth-session-status class="mb-6 rounded-lg border border-vd-success/25 bg-vd-success/10 px-4 py-3 text-body-sm text-vd-success fade-in" :status="session('status')" />

    {{-- ── Login form ── --}}
    <form method="POST" action="{{ route('login') }}" id="login-form" class="space-y-5">
        @csrf

        {{-- ── Email field ── --}}
        <div class="space-y-2">
            <x-input-label for="email" value="Email address" class="text-body-sm" />
            <x-text-input 
                id="email" 
                type="email" 
                name="email"
                :value="old('email')" 
                required 
                autofocus 
                autocomplete="username"
                placeholder="you@example.com" 
                class="vd-input-animated" 
            />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        {{-- ── Password field ── --}}
        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <x-input-label for="password" value="Password" class="text-body-sm" />
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                       class="text-body-sm font-medium text-vd-primary hover:brightness-110 transition-all">
                        Forgot password?
                    </a>
                @endif
            </div>
            <x-text-input 
                id="password" 
                type="password" 
                name="password"
                required 
                autocomplete="current-password" 
                placeholder="••••••••"
                class="vd-input-animated" 
            />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        {{-- ── reCAPTCHA section ── --}}
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

        {{-- ── Remember me checkbox ── --}}
        <div class="flex items-center">
            <label for="remember_me" class="inline-flex items-center gap-2.5 cursor-pointer group">
                <input 
                    id="remember_me" 
                    type="checkbox" 
                    name="remember"
                    class="h-4 w-4 rounded-md border-vd-border-strong bg-vd-secondary text-vd-primary focus:ring-2 focus:ring-vd-primary focus:ring-offset-0 transition cursor-pointer"
                >
                <span class="text-body-sm text-vd-muted group-hover:text-vd-on-surface transition">
                    Remember me
                </span>
            </label>
        </div>

        {{-- ── Submit button ── --}}
        <x-primary-button
            class="w-full justify-center mt-8 vd-btn-animated"
            data-login-submit
            :disabled="$recaptchaEnabled">
            Sign in
        </x-primary-button>
    </form>

    {{-- ── Register link ── --}}
    @if (Route::has('register'))
        <div class="mt-6 text-center">
            <p class="text-body-sm text-vd-muted">
                Return to
                <a href="https://vericotech.com/" class="font-medium text-vd-primary hover:brightness-110 transition-all">
                    VERIDIUM Web
                </a>
                ?
            </p>
        </div>
    @endif

    {{-- ── reCAPTCHA scripts ── --}}
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
