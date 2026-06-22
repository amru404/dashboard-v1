<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DayThreeAuthRoleProtectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeded_admin_logs_in_to_admin_dashboard(): void
    {
        $this->seed();

        $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard', absolute: false));

        $this->assertAuthenticatedAs(User::query()->where('email', 'admin@example.com')->firstOrFail());
    }

    public function test_seeded_customer_logs_in_to_customer_dashboard(): void
    {
        $this->seed();

        $this->post('/login', [
            'email' => 'user@example.com',
            'password' => 'password',
        ])->assertRedirect(route('user.dashboard', absolute: false));

        $this->assertAuthenticatedAs(User::query()->where('email', 'user@example.com')->firstOrFail());
    }

    public function test_admin_and_customer_areas_are_role_protected(): void
    {
        $this->seed();

        $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();
        $customer = User::query()->where('email', 'user@example.com')->firstOrFail();

        $this->actingAs($admin)->get('/admin')->assertOk();
        $this->actingAs($admin)->get('/user')->assertForbidden();

        $this->actingAs($customer)->get('/user')->assertOk();
        $this->actingAs($customer)->get('/admin')->assertForbidden();
    }

    public function test_dashboard_route_redirects_by_role(): void
    {
        $this->seed();

        $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();
        $customer = User::query()->where('email', 'user@example.com')->firstOrFail();

        $this->actingAs($admin)
            ->get('/dashboard')
            ->assertRedirect(route('admin.dashboard', absolute: false));

        $this->actingAs($customer)
            ->get('/dashboard')
            ->assertRedirect(route('user.dashboard', absolute: false));
    }

    public function test_admin_dashboard_shows_day_three_summary_cards(): void
    {
        $this->seed();

        $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Organizations')
            ->assertSee('Users')
            ->assertSee('Products')
            ->assertSee('Licenses');
    }

    public function test_inactive_user_is_logged_out_on_next_protected_request(): void
    {
        $this->seed();

        $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();
        $admin->update(['is_active' => false]);

        $this->actingAs($admin)
            ->get('/admin')
            ->assertRedirect('/login')
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }
}
