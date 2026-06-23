<x-guest-layout>
    <div class="mb-7">
        <p class="text-eyebrow tracking-[0.18em] text-vd-primary uppercase mb-3">Password Reset</p>
        <h1 class="text-headline-sm text-vd-on-surface">Forgot Password?</h1>
        <p class="mt-2 text-body-sm text-vd-muted leading-relaxed">
            Enter your email address and we'll send you a link to reset your password.
        </p>
    </div>

    <x-auth-session-status class="mb-5 rounded-lg border border-vd-success/25 bg-vd-success/10 px-4 py-3 text-body-sm text-vd-success" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf
        <div>
            <x-input-label for="email" value="Email address" />
            <x-text-input id="email" type="email" name="email"
                :value="old('email')" required autofocus
                placeholder="you@example.com" class="mt-1" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>
        <x-primary-button class="w-full justify-center">
            Send Reset Link
        </x-primary-button>
    </form>

    <div class="mt-5 text-center">
        <a href="{{ route('login') }}" class="text-body-sm text-vd-primary hover:brightness-125 transition">
            Back to sign in
        </a>
    </div>
</x-guest-layout>
