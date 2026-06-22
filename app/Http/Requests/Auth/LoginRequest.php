<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];

        if ($this->recaptchaEnabled()) {
            $rules['g-recaptcha-response'] = ['required', 'string'];
        }

        return $rules;
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureRecaptchaIsVerified();
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure Google reCAPTCHA was solved before credentials are checked.
     *
     * @throws ValidationException
     */
    public function ensureRecaptchaIsVerified(): void
    {
        if (! $this->recaptchaEnabled()) {
            return;
        }

        $secret = (string) config('services.recaptcha.secret_key');
        if ($secret === '') {
            throw ValidationException::withMessages([
                'g-recaptcha-response' => 'reCAPTCHA is not configured. Please contact an administrator.',
            ]);
        }

        $token = (string) $this->input('g-recaptcha-response');
        if ($token === '') {
            throw ValidationException::withMessages([
                'g-recaptcha-response' => 'Please complete the reCAPTCHA challenge before logging in.',
            ]);
        }

        $response = Http::asForm()
            ->timeout(5)
            ->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secret,
                'response' => $token,
                'remoteip' => $this->ip(),
            ]);

        if (! $response->ok()) {
            throw ValidationException::withMessages([
                'g-recaptcha-response' => 'Unable to verify reCAPTCHA right now. Please try again.',
            ]);
        }

        $payload = $response->json();
        $success = (bool) ($payload['success'] ?? false);

        if ($success) {
            return;
        }

        $codes = $payload['error-codes'] ?? [];
        $detail = is_array($codes) && $codes !== []
            ? ' ('.implode(', ', array_map('strval', $codes)).')'
            : '';

        throw ValidationException::withMessages([
            'g-recaptcha-response' => 'reCAPTCHA verification failed. Please try again.'.$detail,
        ]);
    }

    private function recaptchaEnabled(): bool
    {
        return (bool) config('services.recaptcha.enabled', false);
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
