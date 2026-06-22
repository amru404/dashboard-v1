<?php

namespace Tests\Feature;

use App\Models\DownloadItem;
use App\Models\Entitlement;
use App\Models\License;
use App\Models\LicenseType;
use App\Models\Organization;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class WeekTwoHandoffWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_to_customer_license_and_private_download_workflow(): void
    {
        Storage::fake('local');

        $systemOrganization = Organization::factory()->create(['name' => 'System Administrator']);
        $customerOrganization = Organization::factory()->create(['name' => 'PT Workflow Customer']);
        $admin = User::factory()->create([
            'organization_id' => $systemOrganization->id,
            'role' => User::ROLE_ADMIN,
        ]);
        $customer = User::factory()->create([
            'organization_id' => $customerOrganization->id,
            'role' => User::ROLE_USER,
            'name' => 'Workflow Customer',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.products.store'), [
                'name' => 'Workflow Platform',
                'description' => 'Root product for the Week 2 handoff workflow.',
                'is_active' => '1',
            ])
            ->assertRedirect();

        $rootProduct = Product::query()->where('name', 'Workflow Platform')->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.products.store'), [
                'parent_id' => $rootProduct->id,
                'name' => 'Workflow Desktop Installer',
                'description' => 'Nested child product for customer delivery.',
                'is_active' => '1',
            ])
            ->assertRedirect();

        $childProduct = Product::query()->where('name', 'Workflow Desktop Installer')->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.license-types.store'), [
                'name' => 'Workflow Enterprise',
                'code' => 'workflow enterprise',
                'is_active' => '1',
            ])
            ->assertRedirect();

        $licenseType = LicenseType::query()->where('code', 'WORKFLOW-ENTERPRISE')->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.licenses.store'), [
                'user_id' => $customer->id,
                'product_id' => $rootProduct->id,
                'sub_product_id' => $childProduct->id,
                'license_type_id' => $licenseType->id,
                'quantity' => 1,
                'max_activations' => 3,
                'expired_date' => now()->addMonth()->toDateString(),
                'license_key' => 'WORK-FLOW-KEY-0001',
            ])
            ->assertRedirect();

        $license = License::query()->where('user_id', $customer->id)->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.entitlements.store'), [
                'user_id' => $customer->id,
                'product_id' => $childProduct->id,
                'start_date' => now()->subDay()->toDateString(),
                'end_date' => now()->addYear()->toDateString(),
                'download_expired_date' => now()->addMonth()->toDateString(),
                'status' => Entitlement::STATUS_ACTIVE,
            ])
            ->assertRedirect();

        $this->actingAs($admin)
            ->post(route('admin.download-items.store'), [
                'product_id' => $childProduct->id,
                'file_name' => 'Workflow Installer.exe',
                'file_upload' => UploadedFile::fake()->create('workflow-installer.exe', 128, 'application/octet-stream'),
                'version' => '1.0.0',
                'expired_date' => now()->addMonth()->toDateString(),
                'is_active' => '1',
            ])
            ->assertRedirect();

        $downloadItem = DownloadItem::query()->where('file_name', 'Workflow Installer.exe')->firstOrFail();

        $this->assertNotSame('WORK-FLOW-KEY-0001', $license->getRawOriginal('license_key'));
        $this->assertNotNull($license->license_key_hash);
        Storage::disk('local')->assertExists($downloadItem->file_path);

        $this->actingAs($customer)
            ->get(route('user.dashboard'))
            ->assertOk()
            ->assertSee('Owned products')
            ->assertSee($childProduct->name)
            ->assertSee('Active licenses')
            ->assertSee('Workflow Installer.exe');

        $this->actingAs($customer)
            ->get(route('user.products.show', $childProduct))
            ->assertOk()
            ->assertSee($childProduct->getCatalogPath())
            ->assertSee($licenseType->name)
            ->assertSee('Workflow Installer.exe');

        $this->actingAs($customer)
            ->get(route('user.licenses.show', $license))
            ->assertOk()
            ->assertSee($license->masked_license_key)
            ->assertSee($licenseType->name);

        $this->actingAs($customer)
            ->get(route('user.downloads.index'))
            ->assertOk()
            ->assertSee('Workflow Installer.exe');

        $this->actingAs($customer)
            ->get(route('user.downloads.download', $downloadItem))
            ->assertOk()
            ->assertDownload('Workflow Installer.exe');

        $this->assertDatabaseHas('download_logs', [
            'user_id' => $customer->id,
            'download_item_id' => $downloadItem->id,
        ]);
    }
}
