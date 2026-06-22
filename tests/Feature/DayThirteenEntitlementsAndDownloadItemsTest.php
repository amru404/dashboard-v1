<?php

namespace Tests\Feature;

use App\Models\DownloadItem;
use App\Models\DownloadLog;
use App\Models\Entitlement;
use App\Models\Organization;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DayThirteenEntitlementsAndDownloadItemsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_grant_update_and_delete_entitlement_with_unique_user_product_pair(): void
    {
        [$admin, $customer, $root, $child] = $this->createFixture();

        $this->actingAs($admin)
            ->get(route('admin.entitlements.create'))
            ->assertOk()
            ->assertSee($customer->email)
            ->assertSee($child->getCatalogPath())
            ->assertSee('This grant controls customer portal product and download access');

        $response = $this->actingAs($admin)
            ->post(route('admin.entitlements.store'), [
                'user_id' => $customer->id,
                'product_id' => $root->id,
                'start_date' => now()->toDateString(),
                'end_date' => now()->addYear()->toDateString(),
                'download_expired_date' => now()->addMonth()->toDateString(),
                'status' => Entitlement::STATUS_ACTIVE,
            ]);

        $entitlement = Entitlement::query()
            ->where('user_id', $customer->id)
            ->where('product_id', $root->id)
            ->firstOrFail();

        $response->assertRedirect(route('admin.entitlements.show', $entitlement, absolute: false));
        $this->assertTrue($entitlement->allowsDownloads());

        $this->actingAs($admin)
            ->post(route('admin.entitlements.store'), [
                'user_id' => $customer->id,
                'product_id' => $root->id,
                'start_date' => now()->toDateString(),
                'status' => Entitlement::STATUS_ACTIVE,
            ])
            ->assertSessionHasErrors('product_id');

        $this->actingAs($admin)
            ->get(route('admin.entitlements.show', $entitlement))
            ->assertOk()
            ->assertSee('Allowed')
            ->assertSee($root->getCatalogPath());

        $this->actingAs($admin)
            ->put(route('admin.entitlements.update', $entitlement), [
                'user_id' => $customer->id,
                'product_id' => $root->id,
                'start_date' => now()->toDateString(),
                'end_date' => now()->addMonth()->toDateString(),
                'download_expired_date' => now()->addWeek()->toDateString(),
                'status' => Entitlement::STATUS_SUSPENDED,
            ])
            ->assertRedirect(route('admin.entitlements.show', $entitlement, absolute: false));

        $entitlement->refresh();

        $this->assertSame(Entitlement::STATUS_SUSPENDED, $entitlement->status);
        $this->assertFalse($entitlement->allowsDownloads());

        $this->actingAs($admin)
            ->delete(route('admin.entitlements.destroy', $entitlement))
            ->assertRedirect(route('admin.entitlements.index', absolute: false))
            ->assertSessionHas('status', 'Entitlement deleted.');

        $this->assertNull($entitlement->fresh());
    }

    public function test_admin_can_upload_private_download_item_and_manage_it(): void
    {
        Storage::fake('local');
        [$admin, $customer, $root] = $this->createFixture();

        $upload = UploadedFile::fake()->create('installer.exe', 128, 'application/octet-stream');

        $response = $this->actingAs($admin)
            ->post(route('admin.download-items.store'), [
                'product_id' => $root->id,
                'user_id' => $customer->id,
                'file_name' => '',
                'file_upload' => $upload,
                'version' => '1.0.0',
                'expired_date' => now()->addMonth()->toDateString(),
                'is_active' => '1',
            ]);

        $downloadItem = DownloadItem::query()->firstOrFail();

        $response->assertRedirect(route('admin.download-items.show', $downloadItem, absolute: false));
        $this->assertSame('installer.exe', $downloadItem->file_name);
        $this->assertStringStartsWith('downloads/', $downloadItem->file_path);
        $this->assertStringNotContainsString('public', $downloadItem->file_path);
        $this->assertGreaterThan(0, $downloadItem->file_size);
        Storage::disk('local')->assertExists($downloadItem->file_path);

        $this->actingAs($admin)
            ->get(route('admin.download-items.index'))
            ->assertOk()
            ->assertSee('storage/app/private/downloads')
            ->assertSee($downloadItem->file_name)
            ->assertSee($customer->name);

        $this->actingAs($admin)
            ->put(route('admin.download-items.update', $downloadItem), [
                'product_id' => $root->id,
                'user_id' => '',
                'file_name' => 'installer-renamed.exe',
                'version' => '1.1.0',
            ])
            ->assertRedirect(route('admin.download-items.show', $downloadItem, absolute: false));

        $downloadItem->refresh();

        $this->assertSame('installer-renamed.exe', $downloadItem->file_name);
        $this->assertSame('1.1.0', $downloadItem->version);
        $this->assertNull($downloadItem->user_id);
        $this->assertFalse($downloadItem->is_active);

        $this->actingAs($admin)
            ->delete(route('admin.download-items.destroy', $downloadItem))
            ->assertRedirect(route('admin.download-items.index', absolute: false));

        $this->assertNull($downloadItem->fresh());
    }

    public function test_admin_can_register_existing_private_download_path_and_invalid_paths_are_blocked(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('downloads/manual-installer.exe', 'private installer bytes');
        [$admin, , $root] = $this->createFixture();

        $this->actingAs($admin)
            ->post(route('admin.download-items.store'), [
                'product_id' => $root->id,
                'file_path' => 'public/manual-installer.exe',
                'file_size' => 100,
                'is_active' => '1',
            ])
            ->assertSessionHasErrors('file_path');

        $this->actingAs($admin)
            ->post(route('admin.download-items.store'), [
                'product_id' => $root->id,
                'file_path' => 'downloads/missing-installer.exe',
                'file_size' => 100,
                'is_active' => '1',
            ])
            ->assertSessionHasErrors('file_path');

        $this->actingAs($admin)
            ->post(route('admin.download-items.store'), [
                'product_id' => $root->id,
                'file_path' => 'downloads/manual-installer.exe',
                'file_size' => 23,
                'version' => '2.0.0',
                'is_active' => '1',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('download_items', [
            'file_name' => 'manual-installer.exe',
            'file_path' => 'downloads/manual-installer.exe',
            'file_size' => 23,
            'version' => '2.0.0',
        ]);
    }

    public function test_download_item_access_rules_and_download_log_helper_are_ready_for_customer_delivery(): void
    {
        [$admin, $customer, $root, , $otherProduct] = $this->createFixture();
        $otherUser = User::factory()->create(['role' => User::ROLE_USER]);

        Entitlement::query()->create([
            'user_id' => $customer->id,
            'product_id' => $root->id,
            'start_date' => now()->subDay()->toDateString(),
            'download_expired_date' => now()->addDay()->toDateString(),
            'status' => Entitlement::STATUS_ACTIVE,
        ]);
        Entitlement::query()->create([
            'user_id' => $customer->id,
            'product_id' => $otherProduct->id,
            'start_date' => now()->subDay()->toDateString(),
            'download_expired_date' => now()->subDay()->toDateString(),
            'status' => Entitlement::STATUS_ACTIVE,
        ]);

        $sharedItem = DownloadItem::query()->create([
            'product_id' => $root->id,
            'file_name' => 'shared.exe',
            'file_path' => 'downloads/shared.exe',
            'is_active' => true,
        ]);
        $customerItem = DownloadItem::query()->create([
            'product_id' => $root->id,
            'user_id' => $customer->id,
            'file_name' => 'customer-only.exe',
            'file_path' => 'downloads/customer-only.exe',
            'is_active' => true,
        ]);
        DownloadItem::query()->create([
            'product_id' => $root->id,
            'user_id' => $otherUser->id,
            'file_name' => 'other-user.exe',
            'file_path' => 'downloads/other-user.exe',
            'is_active' => true,
        ]);
        DownloadItem::query()->create([
            'product_id' => $root->id,
            'file_name' => 'inactive.exe',
            'file_path' => 'downloads/inactive.exe',
            'is_active' => false,
        ]);
        DownloadItem::query()->create([
            'product_id' => $root->id,
            'file_name' => 'expired.exe',
            'file_path' => 'downloads/expired.exe',
            'expired_date' => now()->subDay()->toDateString(),
            'is_active' => true,
        ]);
        DownloadItem::query()->create([
            'product_id' => $otherProduct->id,
            'file_name' => 'download-window-closed.exe',
            'file_path' => 'downloads/download-window-closed.exe',
            'is_active' => true,
        ]);

        $availableFileNames = DownloadItem::query()
            ->availableForUser($customer)
            ->orderBy('id')
            ->pluck('file_name')
            ->all();

        $this->assertSame(['shared.exe', 'customer-only.exe'], $availableFileNames);
        $this->assertTrue($sharedItem->isAvailableForUser($customer));
        $this->assertTrue($customerItem->isAvailableForUser($customer));

        $log = DownloadLog::logDownload($customer->id, $sharedItem->id, '203.0.113.10');

        $this->assertDatabaseHas('download_logs', [
            'id' => $log->id,
            'user_id' => $customer->id,
            'download_item_id' => $sharedItem->id,
            'ip_address' => '203.0.113.10',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.download-items.show', $sharedItem))
            ->assertOk()
            ->assertSee('203.0.113.10')
            ->assertSee($customer->name);
    }

    /**
     * @return array{User, User, Product, Product, Product}
     */
    private function createFixture(): array
    {
        $organization = Organization::factory()->create([
            'name' => 'PT Day Thirteen',
        ]);
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $customer = User::factory()->create([
            'organization_id' => $organization->id,
            'role' => User::ROLE_USER,
        ]);
        $root = Product::query()->create([
            'code' => 'DAY13-ROOT',
            'name' => 'Day 13 Platform',
            'is_active' => true,
        ]);
        $child = Product::query()->create([
            'parent_id' => $root->id,
            'code' => 'DAY13-CHILD',
            'name' => 'Day 13 Desktop',
            'is_active' => true,
        ]);
        $otherProduct = Product::query()->create([
            'code' => 'DAY13-OTHER',
            'name' => 'Other Day 13 Product',
            'is_active' => true,
        ]);

        return [$admin, $customer, $root, $child, $otherProduct];
    }
}
