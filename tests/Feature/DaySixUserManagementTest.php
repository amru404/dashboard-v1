<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DaySixUserManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    }

    public function test_admin_can_create_view_and_delete_user(): void
    {
        $organization = Organization::factory()->create(['name' => 'PT User Management']);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.store'), [
                'organization_id' => $organization->id,
                'name' => 'Managed Customer',
                'email' => 'managed.customer@example.com',
                'password' => 'temporary-password',
                'role' => User::ROLE_USER,
                'is_active' => '1',
            ]);

        $user = User::query()->where('email', 'managed.customer@example.com')->firstOrFail();

        $response->assertRedirect(route('admin.users.show', $user, absolute: false));
        $this->assertTrue(Hash::check('temporary-password', $user->password));

        $this->actingAs($this->admin)
            ->get(route('admin.users.show', $user))
            ->assertOk()
            ->assertSee('Managed Customer')
            ->assertSee('PT User Management');

        $this->actingAs($this->admin)
            ->delete(route('admin.users.destroy', $user))
            ->assertRedirect(route('admin.users.index', absolute: false))
            ->assertSessionHas('status', 'User deleted.');

        $this->assertNull($user->fresh());
    }

    public function test_user_index_can_filter_by_organization_role_status_and_search(): void
    {
        $targetOrganization = Organization::factory()->create();
        $otherOrganization = Organization::factory()->create();

        User::factory()->create([
            'organization_id' => $targetOrganization->id,
            'name' => 'Filtered Customer',
            'email' => 'filtered.customer@example.com',
            'role' => User::ROLE_USER,
            'is_active' => false,
        ]);

        User::factory()->create([
            'organization_id' => $otherOrganization->id,
            'name' => 'Other Customer',
            'email' => 'other.customer@example.com',
            'role' => User::ROLE_USER,
            'is_active' => false,
        ]);

        User::factory()->create([
            'organization_id' => $targetOrganization->id,
            'name' => 'Active Admin',
            'email' => 'active.admin@example.com',
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.users.index', [
                'organization_id' => $targetOrganization->id,
                'role' => User::ROLE_USER,
                'status' => 'inactive',
                'search' => 'filtered',
            ]))
            ->assertOk()
            ->assertSee('Filtered Customer')
            ->assertSee('filtered.customer@example.com')
            ->assertDontSee('Other Customer')
            ->assertDontSee('Active Admin');
    }

    public function test_customer_user_requires_organization(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.users.store'), [
                'name' => 'Unassigned Customer',
                'email' => 'unassigned.customer@example.com',
                'password' => 'temporary-password',
                'role' => User::ROLE_USER,
                'is_active' => '1',
            ])
            ->assertSessionHasErrors('organization_id');
    }

    public function test_admin_can_update_user_and_password_only_changes_when_provided(): void
    {
        $organization = Organization::factory()->create();
        $user = User::factory()->create([
            'organization_id' => $organization->id,
            'password' => Hash::make('old-password'),
            'role' => User::ROLE_USER,
            'is_active' => true,
        ]);
        $originalPassword = $user->password;

        $this->actingAs($this->admin)
            ->put(route('admin.users.update', $user), [
                'organization_id' => $organization->id,
                'name' => 'Updated Customer',
                'email' => $user->email,
                'password' => '',
                'role' => User::ROLE_USER,
                'is_active' => '0',
            ])
            ->assertRedirect(route('admin.users.show', $user, absolute: false));

        $user->refresh();

        $this->assertSame($originalPassword, $user->password);
        $this->assertFalse($user->is_active);

        $this->actingAs($this->admin)
            ->put(route('admin.users.update', $user), [
                'organization_id' => $organization->id,
                'name' => 'Updated Customer',
                'email' => $user->email,
                'password' => 'new-password',
                'role' => User::ROLE_USER,
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.users.show', $user, absolute: false));

        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
    }

    public function test_current_admin_cannot_delete_or_deactivate_self(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.users.edit', $this->admin))
            ->assertOk()
            ->assertSee('Your own account will remain active to prevent lockout.');

        $this->actingAs($this->admin)
            ->put(route('admin.users.update', $this->admin), [
                'name' => $this->admin->name,
                'email' => $this->admin->email,
                'password' => '',
                'role' => User::ROLE_ADMIN,
                'is_active' => '0',
            ])
            ->assertRedirect(route('admin.users.show', $this->admin, absolute: false));

        $this->assertTrue($this->admin->fresh()->is_active);

        $this->actingAs($this->admin)
            ->delete(route('admin.users.destroy', $this->admin))
            ->assertRedirect(route('admin.users.index', absolute: false))
            ->assertSessionHasErrors('user');

        $this->assertNotNull($this->admin->fresh());
    }
}
