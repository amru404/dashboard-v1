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
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DayTwoDatabaseFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_day_two_tables_exist_with_required_columns(): void
    {
        $tables = [
            'organizations' => ['id', 'name', 'code', 'address', 'phone', 'email', 'is_active'],
            'users' => ['id', 'organization_id', 'name', 'email', 'password', 'role', 'is_active'],
            'products' => ['id', 'parent_id', 'code', 'name', 'description', 'is_active'],
            'license_types' => ['id', 'name', 'code', 'is_active'],
            'licenses' => ['id', 'product_id', 'sub_product_id', 'user_id', 'license_type_id', 'license_key', 'license_key_hash', 'client_name', 'quantity', 'max_activations', 'expired_date'],
            'license_activations' => ['id', 'license_id', 'device_id', 'ip_address', 'hostname', 'location', 'status'],
            'entitlements' => ['id', 'user_id', 'product_id', 'start_date', 'end_date', 'download_expired_date', 'status'],
            'download_items' => ['id', 'product_id', 'user_id', 'file_name', 'file_path', 'file_size', 'version', 'expired_date', 'is_active'],
            'download_logs' => ['id', 'user_id', 'download_item_id', 'ip_address', 'downloaded_at'],
        ];

        foreach ($tables as $table => $columns) {
            $this->assertTrue(Schema::hasTable($table), "Missing table: {$table}");

            foreach ($columns as $column) {
                $this->assertTrue(Schema::hasColumn($table, $column), "Missing column: {$table}.{$column}");
            }
        }
    }

    public function test_database_seed_creates_default_organizations_and_users(): void
    {
        $this->seed();

        $this->assertDatabaseHas('organizations', [
            'name' => 'System Administrator',
            'code' => 'SYSADMIN',
        ]);

        $this->assertDatabaseHas('organizations', [
            'name' => 'PT Acme Indonesia',
            'code' => 'ACME',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'admin@example.com',
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'user@example.com',
            'role' => User::ROLE_USER,
            'is_active' => true,
        ]);
    }

    public function test_domain_records_can_be_created_with_expected_relationships(): void
    {
        $organization = Organization::factory()->create();
        $user = User::factory()->create(['organization_id' => $organization->id]);
        $product = Product::query()->create([
            'code' => 'MIMSAN',
            'name' => 'Mimsan Platform',
            'is_active' => true,
        ]);
        $subProduct = Product::query()->create([
            'parent_id' => $product->id,
            'code' => 'MIMSAN-DESKTOP',
            'name' => 'Mimsan Desktop',
            'is_active' => true,
        ]);
        $licenseType = LicenseType::query()->create([
            'name' => 'Standard',
            'code' => 'STANDARD',
            'is_active' => true,
        ]);

        $license = License::query()->create([
            'product_id' => $product->id,
            'sub_product_id' => $subProduct->id,
            'user_id' => $user->id,
            'license_type_id' => $licenseType->id,
            'license_key' => 'encrypted-placeholder',
            'license_key_hash' => hash('sha256', 'TEST-LICENSE-KEY'),
            'client_name' => $organization->name,
            'quantity' => 1,
            'max_activations' => 2,
            'expired_date' => now()->addYear()->toDateString(),
        ]);

        LicenseActivation::query()->create([
            'license_id' => $license->id,
            'device_id' => 'dev-1234567890abcdef',
            'ip_address' => '127.0.0.1',
            'hostname' => 'test-workstation',
            'location' => 'Local',
            'status' => 'active',
        ]);

        Entitlement::query()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'start_date' => now()->toDateString(),
            'status' => 'active',
        ]);

        $downloadItem = DownloadItem::query()->create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'file_name' => 'installer.exe',
            'file_path' => 'downloads/installer.exe',
            'file_size' => 1024,
            'version' => '1.0.0',
            'is_active' => true,
        ]);

        DownloadLog::query()->create([
            'user_id' => $user->id,
            'download_item_id' => $downloadItem->id,
            'ip_address' => '127.0.0.1',
            'downloaded_at' => now(),
        ]);

        $this->assertDatabaseCount('licenses', 1);
        $this->assertDatabaseCount('license_activations', 1);
        $this->assertDatabaseCount('entitlements', 1);
        $this->assertDatabaseCount('download_items', 1);
        $this->assertDatabaseCount('download_logs', 1);
        $this->assertTrue($subProduct->parent->is($product));
        $this->assertTrue($license->user->is($user));
    }

    public function test_self_referencing_product_parent_nulls_when_parent_is_deleted(): void
    {
        $parent = Product::query()->create([
            'code' => 'PARENT',
            'name' => 'Parent Product',
        ]);
        $child = Product::query()->create([
            'parent_id' => $parent->id,
            'code' => 'CHILD',
            'name' => 'Child Product',
        ]);

        $parent->delete();

        $this->assertNull($child->fresh()->parent_id);
    }

    public function test_license_activation_device_is_unique_per_license(): void
    {
        $license = $this->createLicenseFixture();

        LicenseActivation::query()->create([
            'license_id' => $license->id,
            'device_id' => 'dev-duplicate',
        ]);

        $this->expectException(QueryException::class);

        LicenseActivation::query()->create([
            'license_id' => $license->id,
            'device_id' => 'dev-duplicate',
        ]);
    }

    public function test_entitlement_is_unique_per_user_and_product(): void
    {
        $license = $this->createLicenseFixture();

        Entitlement::query()->create([
            'user_id' => $license->user_id,
            'product_id' => $license->product_id,
            'start_date' => now()->toDateString(),
        ]);

        $this->expectException(QueryException::class);

        Entitlement::query()->create([
            'user_id' => $license->user_id,
            'product_id' => $license->product_id,
            'start_date' => now()->toDateString(),
        ]);
    }

    public function test_product_delete_cascades_license_related_records(): void
    {
        $license = $this->createLicenseFixture();

        LicenseActivation::query()->create([
            'license_id' => $license->id,
            'device_id' => 'dev-cascade',
        ]);

        $license->product->delete();

        $this->assertDatabaseCount('licenses', 0);
        $this->assertDatabaseCount('license_activations', 0);
    }

    private function createLicenseFixture(): License
    {
        $organization = Organization::factory()->create();
        $user = User::factory()->create(['organization_id' => $organization->id]);
        $product = Product::query()->create([
            'code' => 'PRODUCT-'.fake()->unique()->numberBetween(1000, 9999),
            'name' => 'Product Fixture',
        ]);
        $licenseType = LicenseType::query()->create([
            'name' => 'Standard '.fake()->unique()->numberBetween(1000, 9999),
            'code' => 'STANDARD-'.fake()->unique()->numberBetween(1000, 9999),
        ]);

        return License::query()->create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'license_type_id' => $licenseType->id,
            'license_key' => 'encrypted-placeholder',
            'license_key_hash' => hash('sha256', fake()->uuid()),
        ]);
    }
}
