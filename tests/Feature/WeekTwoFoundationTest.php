<?php

namespace Tests\Feature;

use App\Models\DownloadItem;
use App\Models\Entitlement;
use App\Models\License;
use App\Models\LicenseActivation;
use App\Models\LicenseType;
use App\Models\Organization;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class WeekTwoFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_license_key_is_encrypted_and_hash_lookup_is_ready_for_api(): void
    {
        [$license] = $this->createDomainFixture();

        $plainLicenseKey = 'CA-TEST-0001';
        $license->update(['license_key' => $plainLicenseKey]);
        $license->refresh();

        $rawLicenseKey = DB::table('licenses')
            ->where('id', $license->id)
            ->value('license_key');

        $this->assertNotSame(License::normalizeLicenseKey($plainLicenseKey), $rawLicenseKey);
        $this->assertSame(License::normalizeLicenseKey($plainLicenseKey), $license->revealLicenseKey());
        $this->assertDatabaseHas('licenses', [
            'id' => $license->id,
            'license_key_hash' => License::licenseKeyHash($plainLicenseKey),
        ]);

        $this->assertTrue(
            License::query()->whereLicenseKey(' ca-test-0001 ')->firstOrFail()->is($license)
        );
    }

    public function test_admin_week_two_foundation_pages_render_domain_records(): void
    {
        [$license, $product, $childProduct, $grandchildProduct, $licenseType, $entitlement, $downloadItem, $activation] = $this->createDomainFixture();
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get(route('admin.products.index'))
            ->assertOk()
            ->assertSee($product->name)
            ->assertSee($childProduct->name)
            ->assertSee($grandchildProduct->name);

        $this->actingAs($admin)
            ->get(route('admin.license-types.index'))
            ->assertOk()
            ->assertSee($licenseType->name);

        $this->actingAs($admin)
            ->get(route('admin.licenses.index'))
            ->assertOk()
            ->assertSee($license->user->name)
            ->assertSee($product->name);

        $this->actingAs($admin)
            ->get(route('admin.licenses.show', $license))
            ->assertOk()
            ->assertSee($license->masked_license_key)
            ->assertDontSee('CA-TEST-0001');

        $this->actingAs($admin)
            ->get(route('admin.entitlements.index'))
            ->assertOk()
            ->assertSee($entitlement->user->name)
            ->assertSee($product->name);

        $this->actingAs($admin)
            ->get(route('admin.download-items.index'))
            ->assertOk()
            ->assertSee($downloadItem->file_name);

        $this->actingAs($admin)
            ->get(route('admin.license-activations.index'))
            ->assertOk()
            ->assertSee($activation->device_id);
    }

    public function test_admin_can_reset_activation_for_customer_support(): void
    {
        [, , , , , , , $activation] = $this->createDomainFixture();
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->delete(route('admin.license-activations.destroy', $activation))
            ->assertRedirect(route('admin.license-activations.index', absolute: false))
            ->assertSessionHas('status', 'Activation reset.');

        $this->assertNull($activation->fresh());
    }

    public function test_customer_portal_only_shows_current_users_products_licenses_and_downloads(): void
    {
        [$license, $product, , , , , $downloadItem] = $this->createDomainFixture();
        $otherOrganization = Organization::factory()->create(['name' => 'Other Organization']);
        $otherUser = User::factory()->create([
            'organization_id' => $otherOrganization->id,
            'role' => User::ROLE_USER,
        ]);
        $otherProduct = Product::query()->create([
            'code' => 'OTHER-PRODUCT',
            'name' => 'Other Product',
            'is_active' => true,
        ]);
        $otherLicenseType = LicenseType::query()->create([
            'name' => 'Other Type',
            'code' => 'OTHER',
            'is_active' => true,
        ]);
        License::query()->create([
            'product_id' => $otherProduct->id,
            'user_id' => $otherUser->id,
            'license_type_id' => $otherLicenseType->id,
            'license_key' => 'CA-OTHER-0001',
        ]);
        Entitlement::query()->create([
            'user_id' => $otherUser->id,
            'product_id' => $otherProduct->id,
            'start_date' => now()->toDateString(),
            'status' => Entitlement::STATUS_ACTIVE,
        ]);
        DownloadItem::query()->create([
            'product_id' => $otherProduct->id,
            'file_name' => 'other-installer.exe',
            'file_path' => 'downloads/other-installer.exe',
            'is_active' => true,
        ]);
        DownloadItem::query()->create([
            'product_id' => $product->id,
            'user_id' => $otherUser->id,
            'file_name' => 'other-user-only.exe',
            'file_path' => 'downloads/other-user-only.exe',
            'is_active' => true,
        ]);

        $this->actingAs($license->user)
            ->get(route('user.products.index'))
            ->assertOk()
            ->assertSee($product->name)
            ->assertDontSee($otherProduct->name);

        $this->actingAs($license->user)
            ->get(route('user.licenses.index'))
            ->assertOk()
            ->assertSee($product->name)
            ->assertDontSee($otherProduct->name)
            ->assertDontSee('CA-TEST-0001');

        $this->actingAs($license->user)
            ->get(route('user.downloads.index'))
            ->assertOk()
            ->assertSee($downloadItem->file_name)
            ->assertDontSee('other-installer.exe')
            ->assertDontSee('other-user-only.exe');
    }

    public function test_customer_cannot_open_product_without_current_entitlement(): void
    {
        [$license] = $this->createDomainFixture();
        $unownedProduct = Product::query()->create([
            'code' => 'UNOWNED',
            'name' => 'Unowned Product',
            'is_active' => true,
        ]);

        $this->actingAs($license->user)
            ->get(route('user.products.show', $unownedProduct))
            ->assertNotFound();
    }

    /**
     * @return array{License, Product, Product, Product, LicenseType, Entitlement, DownloadItem, LicenseActivation}
     */
    private function createDomainFixture(): array
    {
        $organization = Organization::factory()->create(['name' => 'PT Week Two']);
        $user = User::factory()->create([
            'organization_id' => $organization->id,
            'role' => User::ROLE_USER,
        ]);
        $product = Product::query()->create([
            'code' => 'MIMSAN',
            'name' => 'Mimsan Platform',
            'description' => 'Main product family.',
            'is_active' => true,
        ]);
        $childProduct = Product::query()->create([
            'parent_id' => $product->id,
            'code' => 'MIMSAN-DESKTOP',
            'name' => 'Mimsan Desktop',
            'is_active' => true,
        ]);
        $grandchildProduct = Product::query()->create([
            'parent_id' => $childProduct->id,
            'code' => 'MIMSAN-DESKTOP-PRO',
            'name' => 'Mimsan Desktop Pro',
            'is_active' => true,
        ]);
        $licenseType = LicenseType::query()->create([
            'name' => 'Standard',
            'code' => 'STANDARD',
            'is_active' => true,
        ]);
        $license = License::query()->create([
            'product_id' => $product->id,
            'sub_product_id' => $childProduct->id,
            'user_id' => $user->id,
            'license_type_id' => $licenseType->id,
            'license_key' => 'CA-TEST-0001',
            'client_name' => $organization->name,
            'quantity' => 1,
            'max_activations' => 2,
            'expired_date' => now()->addDays(20)->toDateString(),
        ]);
        $entitlement = Entitlement::query()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'start_date' => now()->subDay()->toDateString(),
            'download_expired_date' => now()->addMonth()->toDateString(),
            'status' => Entitlement::STATUS_ACTIVE,
        ]);
        $downloadItem = DownloadItem::query()->create([
            'product_id' => $product->id,
            'file_name' => 'mimsan-installer.exe',
            'file_path' => 'downloads/mimsan-installer.exe',
            'file_size' => 1048576,
            'version' => '1.0.0',
            'is_active' => true,
        ]);
        $activation = LicenseActivation::query()->create([
            'license_id' => $license->id,
            'device_id' => 'device-week-two',
            'hostname' => 'support-workstation',
            'status' => 'active',
        ]);

        return [$license, $product, $childProduct, $grandchildProduct, $licenseType, $entitlement, $downloadItem, $activation];
    }
}
