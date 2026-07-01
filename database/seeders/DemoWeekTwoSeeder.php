<?php

namespace Database\Seeders;

use App\Models\DownloadItem;
use App\Models\DownloadLog;
use App\Models\Entitlement;
use App\Models\License;
use App\Models\LicenseActivation;
use App\Models\LicenseType;
use App\Models\Organization;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DemoWeekTwoSeeder extends Seeder
{
    /**
     * Seed demo data for manual Week 2 browser testing.
     */
    public function run(): void
    {
        $system = Organization::query()->updateOrCreate(
            ['code' => 'SYSADMIN'],
            [
                'name' => 'System Administrator',
                'is_active' => true,
            ],
        );

        $acme = Organization::query()->updateOrCreate(
            ['code' => 'ACME'],
            [
                'name' => 'PT Acme Indonesia',
                'address' => 'Jakarta, Indonesia',
                'phone' => '+62 21 5550 1000',
                'email' => 'it@acme.example',
                'is_active' => true,
            ],
        );

        User::query()->updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'organization_id' => $system->id,
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        );

        $customer = User::query()->updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'organization_id' => $acme->id,
                'name' => 'Customer User',
                'password' => Hash::make('password'),
                'role' => User::ROLE_CLIENT,
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        );

        $licenseTypes = $this->seedLicenseTypes();
        $products = $this->seedProducts();
        $licenses = $this->seedLicenses($customer, $licenseTypes, $products);

        $this->seedEntitlements($customer, $products);
        $downloadItems = $this->seedDownloadItems($customer, $products);
        $this->seedActivations($licenses);
        $this->seedDownloadLogs($customer, $downloadItems);
    }

    /**
     * @return array<string, LicenseType>
     */
    private function seedLicenseTypes(): array
    {
        return collect([
            ['name' => 'Trial', 'code' => 'TRIAL'],
            ['name' => 'Single', 'code' => 'SINGLE'],
            ['name' => 'Multi', 'code' => 'MULTI'],
            ['name' => 'Enterprise', 'code' => 'ENTERPRISE'],
            ['name' => 'Educational', 'code' => 'EDUCATIONAL'],
        ])->mapWithKeys(function (array $licenseType): array {
            $model = LicenseType::query()->updateOrCreate(
                ['code' => $licenseType['code']],
                [
                    'name' => $licenseType['name'],
                    'is_active' => true,
                ],
            );

            return [$licenseType['code'] => $model];
        })->all();
    }

    /**
     * @return array<string, Product>
     */
    private function seedProducts(): array
    {
        $digitalMobileComm = Product::query()->updateOrCreate(
            ['code' => 'DMC'],
            [
                'parent_id' => null,
                'name' => 'Digital Mobile Comm',
                'description' => 'Main product family for mobile command and communication software.',
                'is_active' => true,
            ],
        );

        $cvms = Product::query()->updateOrCreate(
            ['code' => 'DMC-CVMS'],
            [
                'parent_id' => $digitalMobileComm->id,
                'name' => 'CVMS',
                'description' => 'Command video management suite under Digital Mobile Comm.',
                'is_active' => true,
            ],
        );

        $streaming = Product::query()->updateOrCreate(
            ['code' => 'DMC-CVMS-MSVS'],
            [
                'parent_id' => $cvms->id,
                'name' => 'Multi Source Video Streaming',
                'description' => 'Desktop installer package for multi-source video streaming workflows.',
                'is_active' => true,
            ],
        );

        $player = Product::query()->updateOrCreate(
            ['code' => 'DMC-CVMS-PLAYER'],
            [
                'parent_id' => $cvms->id,
                'name' => 'CVMS Player',
                'description' => 'Customer-facing player application for reviewing archived video.',
                'is_active' => true,
            ],
        );

        $legacy = Product::query()->updateOrCreate(
            ['code' => 'DMC-LEGACY-CONTROL'],
            [
                'parent_id' => $digitalMobileComm->id,
                'name' => 'Legacy Control Panel',
                'description' => 'Inactive sample product used to verify status badges and filtering.',
                'is_active' => false,
            ],
        );

        return [
            'dmc' => $digitalMobileComm,
            'cvms' => $cvms,
            'streaming' => $streaming,
            'player' => $player,
            'legacy' => $legacy,
        ];
    }

    /**
     * @param  array<string, LicenseType>  $licenseTypes
     * @param  array<string, Product>  $products
     * @return array<string, License>
     */
    private function seedLicenses(User $customer, array $licenseTypes, array $products): array
    {
        $enterprise = $this->upsertLicense('CA-DEMO-ENT-0001', [
            'user_id' => $customer->id,
            'product_id' => $products['cvms']->id,
            'sub_product_id' => $products['streaming']->id,
            'license_type_id' => $licenseTypes['ENTERPRISE']->id,
            'client_name' => $customer->organization?->name,
            'quantity' => 10,
            'max_activations' => 5,
            'expired_date' => now()->addDays(90)->toDateString(),
        ]);

        $trial = $this->upsertLicense('CA-DEMO-TRIAL-0002', [
            'user_id' => $customer->id,
            'product_id' => $products['cvms']->id,
            'sub_product_id' => $products['player']->id,
            'license_type_id' => $licenseTypes['TRIAL']->id,
            'client_name' => $customer->organization?->name,
            'quantity' => 1,
            'max_activations' => 1,
            'expired_date' => now()->addDays(12)->toDateString(),
        ]);

        $expired = $this->upsertLicense('CA-DEMO-EXPIRED-0003', [
            'user_id' => $customer->id,
            'product_id' => $products['legacy']->id,
            'sub_product_id' => null,
            'license_type_id' => $licenseTypes['SINGLE']->id,
            'client_name' => $customer->organization?->name,
            'quantity' => 1,
            'max_activations' => 1,
            'expired_date' => now()->subDays(10)->toDateString(),
        ]);

        return [
            'enterprise' => $enterprise,
            'trial' => $trial,
            'expired' => $expired,
        ];
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function upsertLicense(string $licenseKey, array $attributes): License
    {
        $license = License::query()->firstOrNew([
            'license_key_hash' => License::licenseKeyHash($licenseKey),
        ]);

        $license->fill(array_merge($attributes, [
            'license_key' => $licenseKey,
        ]));
        $license->save();

        return $license;
    }

    /**
     * @param  array<string, Product>  $products
     */
    private function seedEntitlements(User $customer, array $products): void
    {
        collect([
            [
                'product' => $products['streaming'],
                'status' => Entitlement::STATUS_ACTIVE,
                'start_date' => now()->subDays(14)->toDateString(),
                'end_date' => now()->addYear()->toDateString(),
                'download_expired_date' => now()->addDays(60)->toDateString(),
            ],
            [
                'product' => $products['player'],
                'status' => Entitlement::STATUS_ACTIVE,
                'start_date' => now()->subDays(7)->toDateString(),
                'end_date' => now()->addDays(45)->toDateString(),
                'download_expired_date' => now()->addDays(30)->toDateString(),
            ],
            [
                'product' => $products['legacy'],
                'status' => Entitlement::STATUS_SUSPENDED,
                'start_date' => now()->subYear()->toDateString(),
                'end_date' => now()->subDays(30)->toDateString(),
                'download_expired_date' => now()->subDays(30)->toDateString(),
            ],
        ])->each(function (array $entitlement) use ($customer): void {
            Entitlement::query()->updateOrCreate(
                [
                    'user_id' => $customer->id,
                    'product_id' => $entitlement['product']->id,
                ],
                [
                    'start_date' => $entitlement['start_date'],
                    'end_date' => $entitlement['end_date'],
                    'download_expired_date' => $entitlement['download_expired_date'],
                    'status' => $entitlement['status'],
                ],
            );
        });
    }

    /**
     * @param  array<string, Product>  $products
     * @return array<string, DownloadItem>
     */
    private function seedDownloadItems(User $customer, array $products): array
    {
        $files = [
            'streaming' => [
                'path' => 'downloads/demo/msvs-installer-1.0.0.exe',
                'name' => 'MSVS Installer 1.0.0.exe',
                'contents' => "Demo installer placeholder for Multi Source Video Streaming.\n",
                'product' => $products['streaming'],
                'user_id' => null,
                'version' => '1.0.0',
                'expired_date' => now()->addDays(60)->toDateString(),
                'is_active' => true,
            ],
            'player' => [
                'path' => 'downloads/demo/cvms-player-2.3.0.exe',
                'name' => 'CVMS Player 2.3.0.exe',
                'contents' => "Demo installer placeholder for CVMS Player.\n",
                'product' => $products['player'],
                'user_id' => null,
                'version' => '2.3.0',
                'expired_date' => now()->addDays(30)->toDateString(),
                'is_active' => true,
            ],
            'hotfix' => [
                'path' => 'downloads/demo/acme-msvs-hotfix-1.0.1.exe',
                'name' => 'ACME MSVS Hotfix 1.0.1.exe',
                'contents' => "Customer-specific demo hotfix placeholder for PT Acme Indonesia.\n",
                'product' => $products['streaming'],
                'user_id' => $customer->id,
                'version' => '1.0.1',
                'expired_date' => now()->addDays(14)->toDateString(),
                'is_active' => true,
            ],
            'inactive' => [
                'path' => 'downloads/demo/legacy-control-panel-0.9.0.exe',
                'name' => 'Legacy Control Panel 0.9.0.exe',
                'contents' => "Inactive demo installer placeholder.\n",
                'product' => $products['legacy'],
                'user_id' => null,
                'version' => '0.9.0',
                'expired_date' => now()->subDays(30)->toDateString(),
                'is_active' => false,
            ],
        ];

        return collect($files)->mapWithKeys(function (array $file, string $key): array {
            Storage::disk('local')->put($file['path'], $file['contents']);

            $downloadItem = DownloadItem::query()->updateOrCreate(
                ['file_path' => $file['path']],
                [
                    'product_id' => $file['product']->id,
                    'user_id' => $file['user_id'],
                    'file_name' => $file['name'],
                    'file_size' => Storage::disk('local')->size($file['path']),
                    'version' => $file['version'],
                    'expired_date' => $file['expired_date'],
                    'is_active' => $file['is_active'],
                ],
            );

            return [$key => $downloadItem];
        })->all();
    }

    /**
     * @param  array<string, License>  $licenses
     */
    private function seedActivations(array $licenses): void
    {
        LicenseActivation::query()->updateOrCreate(
            [
                'license_id' => $licenses['enterprise']->id,
                'device_id' => 'ACME-LAPTOP-001',
            ],
            [
                'hostname' => 'acme-video-ops-01',
                'ip_address' => '203.0.113.10',
                'location' => 'Jakarta HQ',
                'status' => 'active',
            ],
        );

        LicenseActivation::query()->updateOrCreate(
            [
                'license_id' => $licenses['trial']->id,
                'device_id' => 'ACME-TRIAL-VM-01',
            ],
            [
                'hostname' => 'acme-trial-vm',
                'ip_address' => '203.0.113.11',
                'location' => 'QA Lab',
                'status' => 'inactive',
            ],
        );
    }

    /**
     * @param  array<string, DownloadItem>  $downloadItems
     */
    private function seedDownloadLogs(User $customer, array $downloadItems): void
    {
        DownloadLog::query()->updateOrCreate(
            [
                'user_id' => $customer->id,
                'download_item_id' => $downloadItems['streaming']->id,
                'ip_address' => '203.0.113.10',
            ],
            [
                'downloaded_at' => now()->subHours(3),
            ],
        );
    }
}
