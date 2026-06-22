<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DayFiveOrganizationManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    }

    public function test_admin_can_view_organization_index_with_actions_and_status_badges(): void
    {
        Organization::factory()->create([
            'name' => 'Active Organization',
            'code' => 'ACTIVE-ORG',
            'is_active' => true,
        ]);
        Organization::factory()->create([
            'name' => 'Inactive Organization',
            'code' => 'INACTIVE-ORG',
            'is_active' => false,
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.organizations.index'))
            ->assertOk()
            ->assertSee('Create organization')
            ->assertSee('Active Organization')
            ->assertSee('Inactive Organization')
            ->assertSee('Active')
            ->assertSee('Inactive')
            ->assertSee('View')
            ->assertSee('Edit');
    }

    public function test_admin_can_create_view_edit_and_deactivate_organization(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.organizations.store'), [
                'name' => 'PT Day Five',
                'code' => 'day five',
                'address' => 'Jakarta',
                'phone' => '021-555-0505',
                'email' => 'dayfive@example.com',
                'is_active' => '1',
            ]);

        $organization = Organization::query()->where('code', 'DAY-FIVE')->firstOrFail();

        $response->assertRedirect(route('admin.organizations.show', $organization, absolute: false));

        $this->actingAs($this->admin)
            ->get(route('admin.organizations.show', $organization))
            ->assertOk()
            ->assertSee('PT Day Five')
            ->assertSee('dayfive@example.com')
            ->assertSee('Related users');

        $this->actingAs($this->admin)
            ->put(route('admin.organizations.update', $organization), [
                'name' => 'PT Day Five Updated',
                'code' => 'DAY-FIVE-UPDATED',
                'address' => 'Bandung',
                'phone' => '022-555-0505',
                'email' => 'updated@example.com',
                'is_active' => '0',
            ])
            ->assertRedirect(route('admin.organizations.show', $organization, absolute: false));

        $this->assertDatabaseHas('organizations', [
            'id' => $organization->id,
            'name' => 'PT Day Five Updated',
            'code' => 'DAY-FIVE-UPDATED',
            'is_active' => false,
        ]);
    }

    public function test_organization_validation_requires_name_unique_code_and_valid_email(): void
    {
        Organization::factory()->create(['code' => 'DUPLICATE']);

        $this->actingAs($this->admin)
            ->post(route('admin.organizations.store'), [
                'name' => '',
                'code' => 'duplicate',
                'email' => 'not-an-email',
            ])
            ->assertSessionHasErrors(['name', 'code', 'email']);
    }

    public function test_show_page_displays_related_users_when_available(): void
    {
        $organization = Organization::factory()->create();
        User::factory()->create([
            'organization_id' => $organization->id,
            'name' => 'Related Customer',
            'email' => 'related@example.com',
            'role' => User::ROLE_USER,
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.organizations.show', $organization))
            ->assertOk()
            ->assertSee('Related users')
            ->assertSee('Related Customer')
            ->assertSee('related@example.com');
    }

    public function test_admin_can_delete_organization_without_users(): void
    {
        $organization = Organization::factory()->create();

        $this->actingAs($this->admin)
            ->delete(route('admin.organizations.destroy', $organization))
            ->assertRedirect(route('admin.organizations.index', absolute: false))
            ->assertSessionHas('status', 'Organization deleted.');

        $this->assertNull($organization->fresh());
    }

    public function test_admin_cannot_delete_organization_with_users(): void
    {
        $organization = Organization::factory()->create();
        User::factory()->create(['organization_id' => $organization->id]);

        $this->actingAs($this->admin)
            ->delete(route('admin.organizations.destroy', $organization))
            ->assertRedirect(route('admin.organizations.show', $organization, absolute: false))
            ->assertSessionHasErrors('organization');

        $this->assertNotNull($organization->fresh());
    }
}
