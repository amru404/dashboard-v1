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

class DayTwelveBatchLicensesAndActivationSupportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_generate_key_from_protected_endpoint(): void
    {
        [$admin, $customer] = $this->createFixture();

        $this->actingAs($customer)
            ->postJson(route('admin.licenses.generate-key'))
            ->assertForbidden();

        $this->actingAs($admin)
            ->postJson(route('admin.licenses.generate-key'))
            ->assertOk()
            ->assertJsonStructure(['license_key'])
            ->assertJsonPath('license_key', fn (string $key): bool => preg_match('/^[A-F0-9]{4}(-[A-F0-9]{4}){3}$/', $key) === 1);
    }

    public function test_admin_can_batch_create_licenses_with_unique_encrypted_keys(): void
    {
        [$admin, $customer, $root, $child, , $licenseType] = $this->createFixture();

        $this->actingAs($admin)
            ->get(route('admin.licenses.batch-create'))
            ->assertOk()
            ->assertSee('Batch issue licenses')
            ->assertSee('Number of license records')
            ->assertSee('Generated key sample')
            ->assertSee($customer->organization->name)
            ->assertSee($child->getCatalogPath());

        $this->actingAs($admin)
            ->post(route('admin.licenses.batch-store'), [
                'user_id' => $customer->id,
                'product_id' => $root->id,
                'sub_product_id' => $child->id,
                'license_type_id' => $licenseType->id,
                'quantity' => 4,
                'max_activations' => 2,
                'expired_date' => now()->addDays(90)->toDateString(),
                'license_count' => 3,
            ])
            ->assertRedirect(route('admin.licenses.index', absolute: false))
            ->assertSessionHas('status', '3 licenses created.');

        $licenses = License::query()
            ->where('user_id', $customer->id)
            ->where('product_id', $root->id)
            ->get();

        $this->assertCount(3, $licenses);
        $this->assertSame([4, 4, 4], $licenses->pluck('quantity')->all());
        $this->assertSame([2, 2, 2], $licenses->pluck('max_activations')->all());
        $this->assertCount(3, $licenses->pluck('license_key_hash')->unique());
        $this->assertSame([$customer->organization->name], $licenses->pluck('client_name')->unique()->values()->all());

        foreach ($licenses as $license) {
            $rawLicenseKey = DB::table('licenses')->where('id', $license->id)->value('license_key');

            $this->assertNotSame($license->revealLicenseKey(), $rawLicenseKey);
            $this->assertMatchesRegularExpression('/^[A-F0-9]{4}(-[A-F0-9]{4}){3}$/', $license->revealLicenseKey());
        }
    }

    public function test_show_key_endpoint_returns_plaintext_or_clear_app_key_error(): void
    {
        [$admin, $customer, $root, $child, , $licenseType] = $this->createFixture();
        $license = $this->createLicense($customer, $root, $child, $licenseType, 'CA-DAY12-SHOW');

        $this->actingAs($admin)
            ->getJson(route('admin.licenses.show-key', $license))
            ->assertOk()
            ->assertJson([
                'license_key' => 'CA-DAY12-SHOW',
                'masked_license_key' => $license->masked_license_key,
            ]);

        DB::table('licenses')
            ->where('id', $license->id)
            ->update(['license_key' => 'not-decryptable']);

        $this->actingAs($admin)
            ->getJson(route('admin.licenses.show-key', $license))
            ->assertUnprocessable()
            ->assertJson([
                'message' => 'License key cannot be decrypted. Check APP_KEY history.',
            ]);
    }

    public function test_license_detail_displays_activation_rows_and_delete_action(): void
    {
        [$admin, $customer, $root, $child, , $licenseType] = $this->createFixture();
        $license = $this->createLicense($customer, $root, $child, $licenseType, 'CA-DAY12-ACTIVATION');
        $activation = LicenseActivation::query()->create([
            'license_id' => $license->id,
            'device_id' => 'device-day12',
            'hostname' => 'customer-laptop',
            'ip_address' => '192.0.2.25',
            'location' => 'Jakarta',
            'status' => 'active',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.licenses.show', $license))
            ->assertOk()
            ->assertSee($activation->device_id)
            ->assertSee($activation->hostname)
            ->assertSee($activation->ip_address)
            ->assertSee($activation->location)
            ->assertSee('Active')
            ->assertSee('Reset activations')
            ->assertSee(route('admin.licenses.activation.destroy', $activation), false);
    }

    public function test_admin_can_reset_all_activations_by_strategy(): void
    {
        [$admin, $customer, $root, $child, , $licenseType] = $this->createFixture();
        $license = $this->createLicense($customer, $root, $child, $licenseType, 'CA-DAY12-RESET');
        $firstActivation = LicenseActivation::query()->create([
            'license_id' => $license->id,
            'device_id' => 'device-one',
            'status' => 'active',
        ]);
        $secondActivation = LicenseActivation::query()->create([
            'license_id' => $license->id,
            'device_id' => 'device-two',
            'status' => 'active',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.licenses.reset-activation', $license), [
                'strategy' => 'deactivate',
            ])
            ->assertRedirect(route('admin.licenses.show', $license, absolute: false))
            ->assertSessionHas('status', '2 activations marked inactive.');

        $this->assertSame('inactive', $firstActivation->fresh()->status);
        $this->assertSame('inactive', $secondActivation->fresh()->status);

        $this->actingAs($admin)
            ->post(route('admin.licenses.reset-activation', $license), [
                'strategy' => 'delete',
            ])
            ->assertRedirect(route('admin.licenses.show', $license, absolute: false))
            ->assertSessionHas('status', '2 activations removed.');

        $this->assertDatabaseMissing('license_activations', [
            'license_id' => $license->id,
        ]);
    }

    public function test_admin_can_delete_single_activation_record(): void
    {
        [$admin, $customer, $root, $child, , $licenseType] = $this->createFixture();
        $license = $this->createLicense($customer, $root, $child, $licenseType, 'CA-DAY12-DELETE-ACT');
        $activation = LicenseActivation::query()->create([
            'license_id' => $license->id,
            'device_id' => 'device-delete',
            'status' => 'active',
        ]);

        $this->actingAs($customer)
            ->delete(route('admin.licenses.activation.destroy', $activation))
            ->assertForbidden();

        $this->actingAs($admin)
            ->delete(route('admin.licenses.activation.destroy', $activation))
            ->assertRedirect(route('admin.licenses.show', $license, absolute: false))
            ->assertSessionHas('status', 'Activation removed.');

        $this->assertNull($activation->fresh());
        $this->assertNotNull($license->fresh());
    }

    /**
     * @return array{User, User, Product, Product, Product, LicenseType}
     */
    private function createFixture(): array
    {
        $organization = Organization::factory()->create([
            'name' => 'PT Day Twelve',
        ]);
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $customer = User::factory()->create([
            'organization_id' => $organization->id,
            'role' => User::ROLE_USER,
        ]);
        $root = Product::query()->create([
            'code' => 'DAY12-ROOT',
            'name' => 'Day 12 Platform',
            'is_active' => true,
        ]);
        $child = Product::query()->create([
            'parent_id' => $root->id,
            'code' => 'DAY12-CHILD',
            'name' => 'Day 12 Desktop',
            'is_active' => true,
        ]);
        $otherRoot = Product::query()->create([
            'code' => 'DAY12-OTHER',
            'name' => 'Other Day 12 Product',
            'is_active' => true,
        ]);
        $licenseType = LicenseType::query()->create([
            'name' => 'Day 12 Standard',
            'code' => 'DAY12-STANDARD',
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
