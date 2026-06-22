<?php

namespace Tests\Feature;

use App\Models\DownloadItem;
use App\Models\DownloadLog;
use App\Models\Entitlement;
use App\Models\License;
use App\Models\LicenseActivation;
use App\Models\LicenseType;
use App\Models\Organization;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DayFourteenCustomerPortalTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_dashboard_shows_products_license_states_downloads_and_history(): void
    {
        [$customer, , $root, $child, , $licenseType, $downloadItem] = $this->createFixture();
        $activeLicense = $this->createLicense($customer, $root, $child, $licenseType, 'CA-DAY14-ACTIVE', now()->addDays(60)->toDateString());
        $this->createLicense($customer, $root, $child, $licenseType, 'CA-DAY14-SOON', now()->addDays(10)->toDateString());
        $this->createLicense($customer, $root, $child, $licenseType, 'CA-DAY14-EXPIRED', now()->subDay()->toDateString());
        DownloadLog::logDownload($customer->id, $downloadItem->id, '203.0.113.14');

        $this->actingAs($customer)
            ->get(route('user.dashboard'))
            ->assertOk()
            ->assertSee('Owned products')
            ->assertSee('Active licenses')
            ->assertSee('Expiring soon')
            ->assertSee('Expired licenses')
            ->assertSee('Downloads')
            ->assertSee($activeLicense->product->name)
            ->assertSee('Recent download history')
            ->assertSee($downloadItem->file_name);
    }

    public function test_customer_license_index_detail_and_key_reveal_are_scoped_to_owner(): void
    {
        [$customer, $otherCustomer, $root, $child, $otherProduct, $licenseType] = $this->createFixture();
        $license = $this->createLicense($customer, $root, $child, $licenseType, 'CA-DAY14-OWNED', now()->addDays(30)->toDateString());
        $otherLicense = $this->createLicense($otherCustomer, $otherProduct, null, $licenseType, 'CA-DAY14-OTHER', now()->addDays(30)->toDateString());
        LicenseActivation::query()->create([
            'license_id' => $license->id,
            'device_id' => 'customer-device',
            'hostname' => 'customer-laptop',
            'status' => 'active',
        ]);

        $this->actingAs($customer)
            ->get(route('user.licenses.index'))
            ->assertOk()
            ->assertSee($license->masked_license_key)
            ->assertSee($root->name)
            ->assertDontSee($otherProduct->name)
            ->assertDontSee('CA-DAY14-OWNED');

        $this->actingAs($customer)
            ->get(route('user.licenses.show', $license))
            ->assertOk()
            ->assertSee($license->masked_license_key)
            ->assertSee($root->getCatalogPath())
            ->assertSee($child->getCatalogPath())
            ->assertSee($licenseType->name)
            ->assertSee('Max activations')
            ->assertSee('Active activations')
            ->assertSee('Reveal key')
            ->assertDontSee('CA-DAY14-OWNED');

        $this->actingAs($customer)
            ->getJson(route('user.licenses.show-key', $license))
            ->assertOk()
            ->assertJson([
                'license_key' => 'CA-DAY14-OWNED',
                'masked_license_key' => $license->masked_license_key,
            ]);

        $this->actingAs($customer)
            ->get(route('user.licenses.show', $otherLicense))
            ->assertNotFound();

        $this->actingAs($customer)
            ->getJson(route('user.licenses.show-key', $otherLicense))
            ->assertNotFound();
    }

    public function test_customer_products_are_limited_to_current_entitlements_and_show_hierarchy_context(): void
    {
        [$customer, , $root, $child, $otherProduct, $licenseType] = $this->createFixture(entitleRoot: false);
        Entitlement::query()->create([
            'user_id' => $customer->id,
            'product_id' => $child->id,
            'start_date' => now()->subDay()->toDateString(),
            'status' => Entitlement::STATUS_ACTIVE,
        ]);
        $license = $this->createLicense($customer, $root, $child, $licenseType, 'CA-DAY14-PRODUCT', now()->addMonth()->toDateString());

        $this->actingAs($customer)
            ->get(route('user.products.index'))
            ->assertOk()
            ->assertSee($child->name)
            ->assertSee($child->getCatalogPath())
            ->assertDontSee($otherProduct->name);

        $this->actingAs($customer)
            ->get(route('user.products.show', $child))
            ->assertOk()
            ->assertSee($child->getCatalogPath())
            ->assertSee($license->licenseType->name);

        $this->actingAs($customer)
            ->get(route('user.products.show', $otherProduct))
            ->assertNotFound();
    }

    public function test_customer_downloads_are_authorized_streamed_from_private_storage_and_logged(): void
    {
        Storage::fake('local');
        [$customer, $otherCustomer, $root, , $otherProduct] = $this->createFixture();

        Storage::disk('local')->put('downloads/shared.exe', 'shared-bytes');
        Storage::disk('local')->put('downloads/customer.exe', 'customer-bytes');
        Storage::disk('local')->put('downloads/other-user.exe', 'other-user-bytes');
        Storage::disk('local')->put('downloads/inactive.exe', 'inactive-bytes');
        Storage::disk('local')->put('downloads/expired.exe', 'expired-bytes');
        Storage::disk('local')->put('downloads/no-entitlement.exe', 'no-entitlement-bytes');

        $sharedItem = DownloadItem::query()->create([
            'product_id' => $root->id,
            'file_name' => 'shared.exe',
            'file_path' => 'downloads/shared.exe',
            'file_size' => 12,
            'is_active' => true,
        ]);
        $customerItem = DownloadItem::query()->create([
            'product_id' => $root->id,
            'user_id' => $customer->id,
            'file_name' => 'customer.exe',
            'file_path' => 'downloads/customer.exe',
            'file_size' => 14,
            'is_active' => true,
        ]);
        $otherUserItem = DownloadItem::query()->create([
            'product_id' => $root->id,
            'user_id' => $otherCustomer->id,
            'file_name' => 'other-user.exe',
            'file_path' => 'downloads/other-user.exe',
            'is_active' => true,
        ]);
        $inactiveItem = DownloadItem::query()->create([
            'product_id' => $root->id,
            'file_name' => 'inactive.exe',
            'file_path' => 'downloads/inactive.exe',
            'is_active' => false,
        ]);
        $expiredItem = DownloadItem::query()->create([
            'product_id' => $root->id,
            'file_name' => 'expired.exe',
            'file_path' => 'downloads/expired.exe',
            'expired_date' => now()->subDay()->toDateString(),
            'is_active' => true,
        ]);
        $noEntitlementItem = DownloadItem::query()->create([
            'product_id' => $otherProduct->id,
            'file_name' => 'no-entitlement.exe',
            'file_path' => 'downloads/no-entitlement.exe',
            'is_active' => true,
        ]);
        $missingFileItem = DownloadItem::query()->create([
            'product_id' => $root->id,
            'file_name' => 'missing.exe',
            'file_path' => 'downloads/missing.exe',
            'is_active' => true,
        ]);

        $this->actingAs($customer)
            ->get(route('user.downloads.index'))
            ->assertOk()
            ->assertSee($sharedItem->file_name)
            ->assertSee($customerItem->file_name)
            ->assertDontSee($otherUserItem->file_name)
            ->assertDontSee($inactiveItem->file_name)
            ->assertDontSee($expiredItem->file_name)
            ->assertDontSee($noEntitlementItem->file_name);

        $this->actingAs($customer)
            ->get(route('user.downloads.download', $sharedItem))
            ->assertOk()
            ->assertDownload('shared.exe');

        $this->assertDatabaseHas('download_logs', [
            'user_id' => $customer->id,
            'download_item_id' => $sharedItem->id,
        ]);

        $this->actingAs($customer)
            ->get(route('user.downloads.download', $customerItem))
            ->assertOk()
            ->assertDownload('customer.exe');

        $this->actingAs($customer)
            ->get(route('user.downloads.download', $otherUserItem))
            ->assertNotFound();

        $this->actingAs($customer)
            ->get(route('user.downloads.download', $inactiveItem))
            ->assertNotFound();

        $this->actingAs($customer)
            ->get(route('user.downloads.download', $expiredItem))
            ->assertNotFound();

        $this->actingAs($customer)
            ->get(route('user.downloads.download', $noEntitlementItem))
            ->assertNotFound();

        $this->actingAs($customer)
            ->get(route('user.downloads.download', $missingFileItem))
            ->assertNotFound();

        $this->assertDatabaseMissing('download_logs', [
            'user_id' => $customer->id,
            'download_item_id' => $missingFileItem->id,
        ]);
    }

    /**
     * @return array{User, User, Product, Product, Product, LicenseType, DownloadItem}
     */
    private function createFixture(bool $entitleRoot = true): array
    {
        $organization = Organization::factory()->create([
            'name' => 'PT Day Fourteen',
        ]);
        $customer = User::factory()->create([
            'organization_id' => $organization->id,
            'role' => User::ROLE_USER,
        ]);
        $otherCustomer = User::factory()->create(['role' => User::ROLE_USER]);
        $root = Product::query()->create([
            'code' => 'DAY14-ROOT',
            'name' => 'Day 14 Platform',
            'is_active' => true,
        ]);
        $child = Product::query()->create([
            'parent_id' => $root->id,
            'code' => 'DAY14-CHILD',
            'name' => 'Day 14 Desktop',
            'is_active' => true,
        ]);
        $otherProduct = Product::query()->create([
            'code' => 'DAY14-OTHER',
            'name' => 'Other Day 14 Product',
            'is_active' => true,
        ]);
        $licenseType = LicenseType::query()->create([
            'name' => 'Day 14 Standard',
            'code' => 'DAY14-STANDARD',
            'is_active' => true,
        ]);

        if ($entitleRoot) {
            Entitlement::query()->create([
                'user_id' => $customer->id,
                'product_id' => $root->id,
                'start_date' => now()->subDay()->toDateString(),
                'download_expired_date' => now()->addDay()->toDateString(),
                'status' => Entitlement::STATUS_ACTIVE,
            ]);
        }

        $downloadItem = DownloadItem::query()->create([
            'product_id' => $root->id,
            'file_name' => 'dashboard-download.exe',
            'file_path' => 'downloads/dashboard-download.exe',
            'is_active' => true,
        ]);

        return [$customer, $otherCustomer, $root, $child, $otherProduct, $licenseType, $downloadItem];
    }

    private function createLicense(User $customer, Product $root, ?Product $child, LicenseType $licenseType, string $licenseKey, ?string $expiredDate): License
    {
        return License::query()->create([
            'user_id' => $customer->id,
            'product_id' => $root->id,
            'sub_product_id' => $child?->id,
            'license_type_id' => $licenseType->id,
            'license_key' => $licenseKey,
            'client_name' => $customer->organization?->name,
            'quantity' => 1,
            'max_activations' => 2,
            'expired_date' => $expiredDate,
        ]);
    }
}
