<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class CustomerAreaFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_redirects_based_on_role(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $customer = User::factory()->create(['role' => User::ROLE_USER]);

        $this->actingAs($admin)
            ->get('/dashboard')
            ->assertRedirect(route('admin.dashboard', absolute: false));

        $this->actingAs($customer)
            ->get('/dashboard')
            ->assertRedirect(route('user.dashboard', absolute: false));
    }

    public function test_role_routes_are_protected(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $customer = User::factory()->create(['role' => User::ROLE_USER]);

        $this->actingAs($admin)->get('/user')->assertForbidden();
        $this->actingAs($customer)->get('/admin')->assertForbidden();
    }

    public function test_license_verify_api_has_day_one_json_placeholder(): void
    {
        $this->postJson('/api/license/verify', [
            'license_key' => 'RANDOM-KEY',
            'device_fingerprint' => 'test-device',
        ])->assertNotFound()
            ->assertJson([
                'valid' => false,
                'error' => 'License not found',
            ]);
    }

    public function test_expired_web_session_redirects_to_login(): void
    {
        Route::post('/test-expired-session', function (): void {
            throw new TokenMismatchException();
        });

        $this->post('/test-expired-session')
            ->assertRedirect(route('login', absolute: false))
            ->assertSessionHasErrors('email');
    }

    public function test_inactive_user_is_logged_out_on_role_route(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'is_active' => false,
        ]);

        $this->actingAs($admin)
            ->get('/admin')
            ->assertRedirect('/login')
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_admin_can_manage_organizations(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)->post(route('admin.organizations.store'), [
            'name' => 'PT Test Indonesia',
            'code' => 'test indonesia',
            'email' => 'office@example.com',
            'phone' => '021-555-0100',
            'address' => 'Jakarta',
            'is_active' => '1',
        ]);

        $organization = Organization::query()->where('code', 'TEST-INDONESIA')->firstOrFail();

        $response->assertRedirect(route('admin.organizations.show', $organization, absolute: false));
        $this->assertDatabaseHas('organizations', [
            'name' => 'PT Test Indonesia',
            'code' => 'TEST-INDONESIA',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->put(route('admin.organizations.update', $organization), [
                'name' => 'PT Test Nusantara',
                'code' => 'TEST-NUSANTARA',
                'is_active' => '0',
            ])
            ->assertRedirect(route('admin.organizations.show', $organization, absolute: false));

        $this->assertDatabaseHas('organizations', [
            'id' => $organization->id,
            'name' => 'PT Test Nusantara',
            'code' => 'TEST-NUSANTARA',
            'is_active' => false,
        ]);
    }

    public function test_admin_can_manage_users_and_cannot_delete_self(): void
    {
        $organization = Organization::factory()->create();
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'organization_id' => $organization->id,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.users.create'))
            ->assertOk()
            ->assertSee('Temporary password')
            ->assertSee('name="password"', false);

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'organization_id' => $organization->id,
            'name' => 'Customer Person',
            'email' => 'customer@example.com',
            'password' => 'password',
            'role' => User::ROLE_USER,
            'is_active' => '1',
        ]);

        $user = User::query()->where('email', 'customer@example.com')->firstOrFail();

        $response->assertRedirect(route('admin.users.show', $user, absolute: false));
        $this->assertTrue($user->isUser());

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $admin))
            ->assertRedirect(route('admin.users.index', absolute: false))
            ->assertSessionHasErrors('user');

        $this->assertNotNull($admin->fresh());
    }

    public function test_organization_with_users_cannot_be_deleted(): void
    {
        $organization = Organization::factory()->create();
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        User::factory()->create(['organization_id' => $organization->id]);

        $this->actingAs($admin)
            ->delete(route('admin.organizations.destroy', $organization))
            ->assertRedirect(route('admin.organizations.show', $organization, absolute: false))
            ->assertSessionHasErrors('organization');

        $this->assertNotNull($organization->fresh());
    }
}
