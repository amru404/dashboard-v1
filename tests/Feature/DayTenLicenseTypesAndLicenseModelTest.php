<?php

namespace Tests\Feature;

use App\Models\License;
use App\Models\LicenseType;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DayTenLicenseTypesAndLicenseModelTest extends TestCase
{
    use RefreshDatabase;

    private int $licenseCounter = 0;

    public function test_default_license_types_are_seeded(): void
    {
        $this->seed();

        foreach (['TRIAL', 'SINGLE', 'MULTI', 'ENTERPRISE', 'EDUCATIONAL'] as $code) {
            $this->assertDatabaseHas('license_types', [
                'code' => $code,
                'is_active' => true,
            ]);
        }
    }

    public function test_admin_can_manage_license_types(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get(route('admin.license-types.index'))
            ->assertOk()
            ->assertSee('Create type');

        $this->actingAs($admin)
            ->get(route('admin.license-types.create'))
            ->assertOk()
            ->assertSee('Create license type');

        $storeResponse = $this->actingAs($admin)
            ->post(route('admin.license-types.store'), [
                'name' => 'Reseller Partner',
                'code' => 'reseller partner',
                'is_active' => '1',
            ]);

        $licenseType = LicenseType::query()->where('code', 'RESELLER-PARTNER')->firstOrFail();

        $storeResponse->assertRedirect(route('admin.license-types.show', $licenseType, absolute: false));
        $this->assertTrue($licenseType->is_active);

        $this->actingAs($admin)
            ->get(route('admin.license-types.show', $licenseType))
            ->assertOk()
            ->assertSee('Reseller Partner')
            ->assertSee('RESELLER-PARTNER');

        $this->actingAs($admin)
            ->get(route('admin.license-types.edit', $licenseType))
            ->assertOk()
            ->assertSee('Edit license type');

        $this->actingAs($admin)
            ->put(route('admin.license-types.update', $licenseType), [
                'name' => 'Reseller',
                'code' => 'reseller',
            ])
            ->assertRedirect(route('admin.license-types.show', $licenseType, absolute: false));

        $this->assertDatabaseHas('license_types', [
            'id' => $licenseType->id,
            'name' => 'Reseller',
            'code' => 'RESELLER',
            'is_active' => false,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.license-types.destroy', $licenseType))
            ->assertRedirect(route('admin.license-types.index', absolute: false))
            ->assertSessionHas('status', 'License type deleted.');

        $this->assertNull($licenseType->fresh());
    }

    public function test_license_type_validation_and_safe_deletion(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $licenseType = LicenseType::query()->create([
            'name' => 'Standard',
            'code' => 'STANDARD',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.license-types.store'), [
                'name' => 'Duplicate Standard',
                'code' => 'standard',
                'is_active' => '1',
            ])
            ->assertSessionHasErrors('code');

        $license = $this->createLicense([
            'license_type_id' => $licenseType->id,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.license-types.show', $licenseType))
            ->assertOk()
            ->assertSee($license->user->name)
            ->assertSee($license->product->name)
            ->assertSee($license->masked_license_key);

        $this->actingAs($admin)
            ->delete(route('admin.license-types.destroy', $licenseType))
            ->assertRedirect(route('admin.license-types.show', $licenseType, absolute: false))
            ->assertSessionHasErrors('license_type');

        $this->assertNotNull($licenseType->fresh());
        $this->assertNotNull($license->fresh());
    }

    public function test_license_key_is_encrypted_hashed_masked_and_lookup_ready(): void
    {
        $license = $this->createLicense([
            'license_key' => ' ca-test-0001 ',
        ]);

        $rawLicenseKey = DB::table('licenses')
            ->where('id', $license->id)
            ->value('license_key');

        $this->assertNotSame('CA-TEST-0001', $rawLicenseKey);
        $this->assertSame('CA-TEST-0001', $license->revealLicenseKey());
        $this->assertSame('****-****-****-0001', $license->masked_license_key);
        $this->assertDatabaseHas('licenses', [
            'id' => $license->id,
            'license_key_hash' => License::licenseKeyHash('ca-test-0001'),
        ]);
        $this->assertTrue(
            License::query()->whereLicenseKey(' ca-test-0001 ')->firstOrFail()->is($license)
        );
        $this->assertMatchesRegularExpression('/^[A-F0-9]{4}(-[A-F0-9]{4}){3}$/', License::generateKey());
    }

    public function test_invalid_encrypted_license_key_returns_null_and_unavailable_mask(): void
    {
        $license = $this->createLicense();

        DB::table('licenses')
            ->where('id', $license->id)
            ->update(['license_key' => 'not-an-encrypted-value']);

        $license->refresh();

        $this->assertNull($license->license_key);
        $this->assertSame('Unavailable', $license->masked_license_key);
    }

    public function test_license_expiry_helpers_and_scopes(): void
    {
        $neverExpires = $this->createLicense([
            'license_key' => 'CA-DAY10-NEVER',
            'expired_date' => null,
        ]);
        $expiringSoon = $this->createLicense([
            'license_key' => 'CA-DAY10-SOON',
            'expired_date' => now()->addDays(10)->toDateString(),
        ]);
        $future = $this->createLicense([
            'license_key' => 'CA-DAY10-FUTURE',
            'expired_date' => now()->addDays(45)->toDateString(),
        ]);
        $expired = $this->createLicense([
            'license_key' => 'CA-DAY10-EXPIRED',
            'expired_date' => now()->subDay()->toDateString(),
        ]);

        $this->assertFalse($neverExpires->isExpired());
        $this->assertFalse($expiringSoon->isExpired());
        $this->assertTrue($expired->isExpired());
        $this->assertNull($neverExpires->daysUntilExpiry());
        $this->assertSame(10, $expiringSoon->daysUntilExpiry());

        $this->assertTrue(License::query()->active()->pluck('id')->contains($neverExpires->id));
        $this->assertTrue(License::query()->active()->pluck('id')->contains($expiringSoon->id));
        $this->assertTrue(License::query()->active()->pluck('id')->contains($future->id));
        $this->assertFalse(License::query()->active()->pluck('id')->contains($expired->id));

        $this->assertTrue(License::query()->expired()->pluck('id')->contains($expired->id));
        $this->assertFalse(License::query()->expired()->pluck('id')->contains($expiringSoon->id));

        $this->assertTrue(License::query()->expiringSoon()->pluck('id')->contains($expiringSoon->id));
        $this->assertFalse(License::query()->expiringSoon()->pluck('id')->contains($future->id));
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function createLicense(array $overrides = []): License
    {
        $this->licenseCounter++;

        $product = Product::query()->create([
            'code' => 'DAY10-PRODUCT-'.$this->licenseCounter,
            'name' => 'Day 10 Product '.$this->licenseCounter,
            'is_active' => true,
        ]);
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $licenseType = LicenseType::query()->create([
            'name' => 'Day 10 Type '.$this->licenseCounter,
            'code' => 'DAY10-TYPE-'.$this->licenseCounter,
            'is_active' => true,
        ]);

        return License::query()->create(array_merge([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'license_type_id' => $licenseType->id,
            'license_key' => 'CA-DAY10-'.str_pad((string) $this->licenseCounter, 4, '0', STR_PAD_LEFT),
            'quantity' => 1,
            'max_activations' => 2,
        ], $overrides));
    }
}
