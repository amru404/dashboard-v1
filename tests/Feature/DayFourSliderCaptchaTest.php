<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DayFourSliderCaptchaTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_is_blocked_until_recaptcha_is_completed(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        config()->set('services.recaptcha.enabled', true);
        config()->set('services.recaptcha.site_key', 'test-site');
        config()->set('services.recaptcha.secret_key', 'test-secret');

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertSessionHasErrors('g-recaptcha-response');

        $this->assertGuest();
    }

    public function test_login_fails_with_invalid_recaptcha_token(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        config()->set('services.recaptcha.enabled', true);
        config()->set('services.recaptcha.site_key', 'test-site');
        config()->set('services.recaptcha.secret_key', 'test-secret');

        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => false,
                'error-codes' => ['invalid-input-response'],
            ], 200),
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
            'g-recaptcha-response' => 'bad-token',
        ])->assertSessionHasErrors('g-recaptcha-response');

        $this->assertGuest();
    }

    public function test_login_succeeds_after_valid_recaptcha(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        config()->set('services.recaptcha.enabled', true);
        config()->set('services.recaptcha.site_key', 'test-site');
        config()->set('services.recaptcha.secret_key', 'test-secret');

        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
            ], 200),
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
            'g-recaptcha-response' => 'good-token',
        ])->assertRedirect(route('user.dashboard', absolute: false));

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_page_uses_google_recaptcha_interface_when_enabled(): void
    {
        config()->set('services.recaptcha.enabled', true);
        config()->set('services.recaptcha.site_key', 'test-site');

        $this->get('/login')
            ->assertOk()
            ->assertSee('Customer Area')
            ->assertSee('Secure access')
            ->assertSee('Security check')
            ->assertSee('g-recaptcha', false);
    }
}
