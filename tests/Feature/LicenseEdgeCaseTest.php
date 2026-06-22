<?php

namespace Tests\Feature;

use App\Models\License;
use App\Models\LicenseActivation;
use App\Models\LicenseType;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LicenseEdgeCaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_license_encryption_hashing_masking_and_corrupt_payload_edge_cases(): void
    {
        $license = $this->createLicense([
            'license_key' => '  ca-edge-key-0001  ',
        ]);

        $rawLicenseKey = DB::table('licenses')
            ->where('id', $license->id)
            ->value('license_key');

        $this->assertNotSame('CA-EDGE-KEY-0001', $rawLicenseKey);
        $this->assertNotSame(License::licenseKeyHash('CA-EDGE-KEY-0001'), $rawLicenseKey);
        $this->assertSame('CA-EDGE-KEY-0001', $license->revealLicenseKey());
        $this->assertSame('****-****-****-0001', $license->masked_license_key);
        $this->assertSame(License::licenseKeyHash(' ca-edge-key-0001 '), $license->license_key_hash);

        $this->assertTrue(
            License::query()->whereLicenseKey("\nca-edge-key-0001\t")->firstOrFail()->is($license)
        );

        DB::table('licenses')
            ->where('id', $license->id)
            ->update(['license_key' => 'corrupt-encrypted-payload']);

        $license->refresh();

        $this->assertNull($license->revealLicenseKey());
        $this->assertSame('Unavailable', $license->masked_license_key);
    }

    public function test_activation_limits_count_only_active_devices_and_allow_same_device_idempotently(): void
    {
        $license = $this->createLicense([
            'license_key' => 'CA-EDGE-LIMIT-0001',
            'max_activations' => 2,
        ]);

        LicenseActivation::query()->create([
            'license_id' => $license->id,
            'device_id' => ' device-a ',
            'status' => LicenseActivation::STATUS_ACTIVE,
        ]);
        LicenseActivation::query()->create([
            'license_id' => $license->id,
            'device_id' => 'device-b',
            'status' => LicenseActivation::STATUS_INACTIVE,
        ]);

        $license->refresh();

        $this->assertSame(1, $license->activeActivationCount());
        $this->assertSame(1, $license->remainingActivations());
        $this->assertFalse($license->activationLimitReached());
        $this->assertTrue($license->hasActiveActivationForDevice('device-a'));
        $this->assertTrue($license->canActivateDevice('device-a'));
        $this->assertTrue($license->canActivateDevice('device-b'));
        $this->assertFalse($license->canActivateDevice(''));

        LicenseActivation::query()->create([
            'license_id' => $license->id,
            'device_id' => 'device-c',
            'status' => LicenseActivation::STATUS_ACTIVE,
        ]);

        $license->refresh();

        $this->assertSame(2, $license->activeActivationCount());
        $this->assertSame(0, $license->remainingActivations());
        $this->assertTrue($license->activationLimitReached());
        $this->assertTrue($license->canActivateDevice('device-a'));
        $this->assertFalse($license->canActivateDevice('device-d'));
    }

    public function test_unlimited_and_expired_license_activation_edges(): void
    {
        $unlimited = $this->createLicense([
            'license_key' => 'CA-EDGE-UNLIMITED',
            'max_activations' => null,
        ]);
        $expired = $this->createLicense([
            'license_key' => 'CA-EDGE-EXPIRED',
            'max_activations' => 5,
            'expired_date' => now()->subDay()->toDateString(),
        ]);

        foreach (range(1, 3) as $deviceNumber) {
            LicenseActivation::query()->create([
                'license_id' => $unlimited->id,
                'device_id' => 'unlimited-device-'.$deviceNumber,
                'status' => LicenseActivation::STATUS_ACTIVE,
            ]);
        }

        LicenseActivation::query()->create([
            'license_id' => $expired->id,
            'device_id' => 'expired-device',
            'status' => LicenseActivation::STATUS_ACTIVE,
        ]);

        $unlimited->refresh();
        $expired->refresh();

        $this->assertNull($unlimited->remainingActivations());
        $this->assertFalse($unlimited->activationLimitReached());
        $this->assertTrue($unlimited->canActivateDevice('new-unlimited-device'));

        $this->assertTrue($expired->isExpired());
        $this->assertFalse($expired->canActivateDevice('expired-device'));
        $this->assertFalse($expired->canActivateDevice('new-expired-device'));
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function createLicense(array $overrides = []): License
    {
        $product = Product::query()->create([
            'code' => 'EDGE-PRODUCT-'.str()->random(8),
            'name' => 'Edge Product',
            'is_active' => true,
        ]);
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $licenseType = LicenseType::query()->create([
            'name' => 'Edge Type '.str()->random(8),
            'code' => 'EDGE-TYPE-'.str()->random(8),
            'is_active' => true,
        ]);

        return License::query()->create(array_merge([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'license_type_id' => $licenseType->id,
            'license_key' => 'CA-EDGE-DEFAULT',
            'quantity' => 1,
            'max_activations' => 2,
            'expired_date' => null,
        ], $overrides));
    }
}
