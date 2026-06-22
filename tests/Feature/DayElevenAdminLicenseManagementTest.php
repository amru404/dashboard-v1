<?php

namespace Tests\Feature;

use App\Models\License;
use App\Models\LicenseActivation;
use App\Models\LicenseType;
use App\Models\Organization;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DayElevenAdminLicenseManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_license_create_screen_contains_customer_product_type_and_generator_controls(): void
    {
        [$admin, $customer, $root, $child, , $licenseType] = $this->createFixture();

        $this->actingAs($admin)
            ->get(route('admin.licenses.create'))
            ->assertOk()
            ->assertSee($customer->name)
            ->assertSee($customer->email)
            ->assertSee($customer->organization->name)
            ->assertSee($root->name)
            ->assertSee($child->getCatalogPath())
            ->assertSee($licenseType->name)
            ->assertSee('Generate Key')
            ->assertSee('Sub-product');
    }

    public function test_admin_can_issue_license_and_plaintext_is_revealed_only_through_endpoint(): void
    {
        [$admin, $customer, $root, $child, , $licenseType] = $this->createFixture();

        $response = $this->actingAs($admin)
            ->post(route('admin.licenses.store'), [
                'user_id' => $customer->id,
                'product_id' => $root->id,
                'sub_product_id' => $child->id,
                'license_type_id' => $licenseType->id,
                'quantity' => 2,
                'max_activations' => 3,
                'expired_date' => now()->addDays(20)->toDateString(),
                'license_key' => 'CA-DAY11-0001',
            ]);

        $license = License::query()->whereLicenseKey('ca-day11-0001')->firstOrFail();

        $response->assertRedirect(route('admin.licenses.show', $license, absolute: false));
        $this->assertSame($customer->organization->name, $license->client_name);
        $this->assertSame(2, $license->quantity);
        $this->assertSame(3, $license->max_activations);

        $rawLicenseKey = DB::table('licenses')->where('id', $license->id)->value('license_key');

        $this->assertNotSame('CA-DAY11-0001', $rawLicenseKey);
        $this->assertSame(License::licenseKeyHash('CA-DAY11-0001'), $license->license_key_hash);

        $this->actingAs($admin)
            ->get(route('admin.licenses.index'))
            ->assertOk()
            ->assertSee($license->masked_license_key)
            ->assertSee($customer->organization->name)
            ->assertSee($root->name)
            ->assertSee($child->name)
            ->assertDontSee('CA-DAY11-0001');

        $this->actingAs($admin)
            ->get(route('admin.licenses.show', $license))
            ->assertOk()
            ->assertSee($license->masked_license_key)
            ->assertSee($root->getCatalogPath())
            ->assertSee($child->getCatalogPath())
            ->assertSee('Activation support')
            ->assertSee('No activations recorded yet')
            ->assertDontSee('CA-DAY11-0001');

        $this->actingAs($admin)
            ->postJson(route('admin.licenses.reveal-key', $license))
            ->assertOk()
            ->assertJson([
                'license_key' => 'CA-DAY11-0001',
                'masked_license_key' => $license->masked_license_key,
            ]);
    }

    public function test_admin_can_update_license_without_replacing_key_and_can_rotate_key_when_provided(): void
    {
        [$admin, $customer, $root, $child, , $licenseType] = $this->createFixture();
        $license = $this->createLicense($customer, $root, $child, $licenseType, 'CA-DAY11-0002');
        $originalHash = $license->license_key_hash;

        $this->actingAs($admin)
            ->put(route('admin.licenses.update', $license), [
                'user_id' => $customer->id,
                'product_id' => $root->id,
                'sub_product_id' => $child->id,
                'license_type_id' => $licenseType->id,
                'quantity' => 5,
                'max_activations' => '',
                'expired_date' => '',
                'license_key' => '',
            ])
            ->assertRedirect(route('admin.licenses.show', $license, absolute: false));

        $license->refresh();

        $this->assertSame($originalHash, $license->license_key_hash);
        $this->assertSame('CA-DAY11-0002', $license->revealLicenseKey());
        $this->assertSame(5, $license->quantity);
        $this->assertNull($license->max_activations);
        $this->assertNull($license->expired_date);

        $this->actingAs($admin)
            ->put(route('admin.licenses.update', $license), [
                'user_id' => $customer->id,
                'product_id' => $root->id,
                'sub_product_id' => $child->id,
                'license_type_id' => $licenseType->id,
                'quantity' => 5,
                'max_activations' => 8,
                'expired_date' => now()->addDays(45)->toDateString(),
                'license_key' => 'CA-DAY11-ROTATED',
            ])
            ->assertRedirect(route('admin.licenses.show', $license, absolute: false));

        $license->refresh();

        $this->assertNotSame($originalHash, $license->license_key_hash);
        $this->assertSame('CA-DAY11-ROTATED', $license->revealLicenseKey());
        $this->assertSame(8, $license->max_activations);
    }

    public function test_license_validation_blocks_duplicate_keys_and_invalid_sub_products(): void
    {
        [$admin, $customer, $root, $child, $otherRoot, $licenseType] = $this->createFixture();
        $this->createLicense($customer, $root, $child, $licenseType, 'CA-DAY11-DUPLICATE');

        $this->actingAs($admin)
            ->post(route('admin.licenses.store'), [
                'user_id' => $customer->id,
                'product_id' => $root->id,
                'sub_product_id' => $child->id,
                'license_type_id' => $licenseType->id,
                'quantity' => 1,
                'max_activations' => 1,
                'license_key' => ' ca-day11-duplicate ',
            ])
            ->assertSessionHasErrors('license_key');

        $this->actingAs($admin)
            ->post(route('admin.licenses.store'), [
                'user_id' => $customer->id,
                'product_id' => $root->id,
                'sub_product_id' => $otherRoot->id,
                'license_type_id' => $licenseType->id,
                'quantity' => 1,
                'max_activations' => 1,
                'license_key' => 'CA-DAY11-INVALID-SUB',
            ])
            ->assertSessionHasErrors('sub_product_id');
    }

    public function test_admin_can_delete_license_and_activation_records_cascade(): void
    {
        [$admin, $customer, $root, $child, , $licenseType] = $this->createFixture();
        $license = $this->createLicense($customer, $root, $child, $licenseType, 'CA-DAY11-DELETE');
        $activation = LicenseActivation::query()->create([
            'license_id' => $license->id,
            'device_id' => 'day11-device',
            'hostname' => 'support-workstation',
            'status' => 'active',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.licenses.destroy', $license))
            ->assertRedirect(route('admin.licenses.index', absolute: false))
            ->assertSessionHas('status', 'License deleted.');

        $this->assertNull($license->fresh());
        $this->assertNull($activation->fresh());
    }

    public function test_license_reveal_endpoint_is_admin_protected(): void
    {
        [$admin, $customer, $root, $child, , $licenseType] = $this->createFixture();
        $license = $this->createLicense($customer, $root, $child, $licenseType, 'CA-DAY11-PROTECTED');

        $this->actingAs($customer)
            ->postJson(route('admin.licenses.reveal-key', $license))
            ->assertForbidden();

        $this->actingAs($admin)
            ->postJson(route('admin.licenses.reveal-key', $license))
            ->assertOk()
            ->assertJson(['license_key' => 'CA-DAY11-PROTECTED']);
    }

    /**
     * @return array{User, User, Product, Product, Product, LicenseType}
     */
    private function createFixture(): array
    {
        $organization = Organization::factory()->create([
            'name' => 'PT Day Eleven',
        ]);
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $customer = User::factory()->create([
            'organization_id' => $organization->id,
            'role' => User::ROLE_USER,
        ]);
        $root = Product::query()->create([
            'code' => 'DAY11-ROOT',
            'name' => 'Day 11 Platform',
            'is_active' => true,
        ]);
        $child = Product::query()->create([
            'parent_id' => $root->id,
            'code' => 'DAY11-CHILD',
            'name' => 'Day 11 Desktop',
            'is_active' => true,
        ]);
        $otherRoot = Product::query()->create([
            'code' => 'DAY11-OTHER',
            'name' => 'Other Day 11 Product',
            'is_active' => true,
        ]);
        $licenseType = LicenseType::query()->create([
            'name' => 'Day 11 Standard',
            'code' => 'DAY11-STANDARD',
            'is_active' => true,
        ]);

        return [$admin, $customer, $root, $child, $otherRoot, $licenseType];
    }

    private function createLicense(User $customer, Product $root, Product $child, LicenseType $licenseType, string $licenseKey): License
    {
        return License::query()->create([
            'user_id' => $customer->id,
            'product_id' => $root->id,
            'sub_product_id' => $child->id,
            'license_type_id' => $licenseType->id,
            'license_key' => $licenseKey,
            'client_name' => $customer->organization?->name,
            'quantity' => 1,
            'max_activations' => 2,
        ]);
    }
}
