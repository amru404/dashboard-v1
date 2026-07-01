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

class TestDataSeeder extends Seeder
{
    /**
     * Seed a broad, repeatable local dataset for demos and manual testing.
     */
    public function run(): void
    {
        $organizations = $this->seedOrganizations();
        $users = $this->seedUsers($organizations);
        $licenseTypes = $this->seedLicenseTypes();
        $products = $this->seedProducts();
        $licenses = $this->seedLicenses($users, $licenseTypes, $products);

        $this->seedEntitlements($users, $products);
        $downloadItems = $this->seedDownloadItems($users, $products);
        $this->seedActivations($licenses);
        $this->seedDownloadLogs($users, $downloadItems);
    }

    /**
     * @return array<string, Organization>
     */
    private function seedOrganizations(): array
    {
        return collect([
            'system' => [
                'code' => 'SYSADMIN',
                'name' => 'System Administrator',
                'address' => 'Internal operations',
                'phone' => '+62 21 5550 0001',
                'email' => 'admin@example.com',
                'is_active' => true,
            ],
            'acme' => [
                'code' => 'ACME',
                'name' => 'PT Acme Indonesia',
                'address' => 'Jakarta, Indonesia',
                'phone' => '+62 21 5550 1000',
                'email' => 'it@acme.example',
                'is_active' => true,
            ],
            'nusantara' => [
                'code' => 'NUSANTARA',
                'name' => 'PT Nusantara Logistics',
                'address' => 'Surabaya, Indonesia',
                'phone' => '+62 31 5550 2200',
                'email' => 'systems@nusantara.example',
                'is_active' => true,
            ],
            'sagara' => [
                'code' => 'SAGARA',
                'name' => 'Sagara Health Systems',
                'address' => 'Bandung, Indonesia',
                'phone' => '+62 22 5550 3300',
                'email' => 'technology@sagara.example',
                'is_active' => true,
            ],
            'metro' => [
                'code' => 'METRO-GOV',
                'name' => 'Metro City Command Center',
                'address' => 'Bekasi, Indonesia',
                'phone' => '+62 21 5550 4400',
                'email' => 'command@metro-gov.example',
                'is_active' => true,
            ],
            'legacy' => [
                'code' => 'LEGACY-CLIENT',
                'name' => 'Legacy Client Archive',
                'address' => 'Archived customer account',
                'phone' => null,
                'email' => 'archive@legacy.example',
                'is_active' => false,
            ],
        ])->mapWithKeys(function (array $organization, string $key): array {
            $model = Organization::query()->updateOrCreate(
                ['code' => $organization['code']],
                [
                    'name' => $organization['name'],
                    'address' => $organization['address'],
                    'phone' => $organization['phone'],
                    'email' => $organization['email'],
                    'is_active' => $organization['is_active'],
                ],
            );

            return [$key => $model];
        })->all();
    }

    /**
     * @param  array<string, Organization>  $organizations
     * @return array<string, User>
     */
    private function seedUsers(array $organizations): array
    {
        return collect([
            'admin' => [
                'organization' => 'system',
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'role' => User::ROLE_ADMIN,
                'is_active' => true,
                'verified' => true,
            ],
            'ops_admin' => [
                'organization' => 'system',
                'name' => 'Operations Admin',
                'email' => 'admin.ops@example.com',
                'role' => User::ROLE_ADMIN,
                'is_active' => true,
                'verified' => true,
            ],
            'customer' => [
                'organization' => 'acme',
                'name' => 'Customer User',
                'email' => 'user@example.com',
                'role' => User::ROLE_CLIENT,
                'is_active' => true,
                'verified' => true,
            ],
            'acme_ops' => [
                'organization' => 'acme',
                'name' => 'Ari Santoso',
                'email' => 'ops@acme.example',
                'role' => User::ROLE_CLIENT,
                'is_active' => true,
                'verified' => true,
            ],
            'acme_finance' => [
                'organization' => 'acme',
                'name' => 'Maya Putri',
                'email' => 'finance@acme.example',
                'role' => User::ROLE_CLIENT,
                'is_active' => true,
                'verified' => true,
            ],
            'nusantara_dispatch' => [
                'organization' => 'nusantara',
                'name' => 'Bima Pratama',
                'email' => 'dispatch@nusantara.example',
                'role' => User::ROLE_PARTNER,
                'is_active' => true,
                'verified' => true,
            ],
            'sagara_qa' => [
                'organization' => 'sagara',
                'name' => 'Nadia Rahman',
                'email' => 'qa@sagara.example',
                'role' => User::ROLE_CLIENT,
                'is_active' => true,
                'verified' => true,
            ],
            'sagara_trial' => [
                'organization' => 'sagara',
                'name' => 'Trial Evaluator',
                'email' => 'trial@sagara.example',
                'role' => User::ROLE_PARTNER,
                'is_active' => true,
                'verified' => false,
            ],
            'metro_command' => [
                'organization' => 'metro',
                'name' => 'Rafi Hidayat',
                'email' => 'command@metro-gov.example',
                'role' => User::ROLE_CLIENT,
                'is_active' => true,
                'verified' => true,
            ],
            'inactive_customer' => [
                'organization' => 'legacy',
                'name' => 'Inactive Customer',
                'email' => 'inactive.customer@example.com',
                'role' => User::ROLE_PARTNER,
                'is_active' => false,
                'verified' => true,
            ],
        ])->mapWithKeys(function (array $user, string $key) use ($organizations): array {
            $model = User::query()->firstOrNew(['email' => $user['email']]);
            $model->fill([
                'organization_id' => $organizations[$user['organization']]->id,
                'name' => $user['name'],
                'password' => Hash::make('password'),
                'role' => $user['role'],
                'is_active' => $user['is_active'],
            ]);
            $model->forceFill([
                'email_verified_at' => $user['verified'] ? now() : null,
            ]);
            $model->save();

            return [$key => $model];
        })->all();
    }

    /**
     * @return array<string, LicenseType>
     */
    private function seedLicenseTypes(): array
    {
        return collect([
            'TRIAL' => ['name' => 'Trial', 'is_active' => true],
            'SINGLE' => ['name' => 'Single', 'is_active' => true],
            'MULTI' => ['name' => 'Multi', 'is_active' => true],
            'ENTERPRISE' => ['name' => 'Enterprise', 'is_active' => true],
            'EDUCATIONAL' => ['name' => 'Educational', 'is_active' => true],
            'SITE' => ['name' => 'Site License', 'is_active' => true],
            'MAINTENANCE' => ['name' => 'Maintenance', 'is_active' => true],
            'RESELLER' => ['name' => 'Reseller Partner', 'is_active' => true],
            'EVALUATION' => ['name' => 'Internal Evaluation', 'is_active' => false],
        ])->mapWithKeys(function (array $licenseType, string $code): array {
            $model = LicenseType::query()->updateOrCreate(
                ['code' => $code],
                [
                    'name' => $licenseType['name'],
                    'is_active' => $licenseType['is_active'],
                ],
            );

            return [$code => $model];
        })->all();
    }

    /**
     * @return array<string, Product>
     */
    private function seedProducts(): array
    {
        $products = [];

        $products['dmc'] = $this->upsertProduct('DMC', [
            'parent_id' => null,
            'name' => 'Digital Mobile Comm',
            'description' => 'Main product family for mobile command and communication software.',
            'is_active' => true,
        ]);
        $products['cvms'] = $this->upsertProduct('DMC-CVMS', [
            'parent_id' => $products['dmc']->id,
            'name' => 'CVMS',
            'description' => 'Command video management suite under Digital Mobile Comm.',
            'is_active' => true,
        ]);
        $products['streaming'] = $this->upsertProduct('DMC-CVMS-MSVS', [
            'parent_id' => $products['cvms']->id,
            'name' => 'Multi Source Video Streaming',
            'description' => 'Desktop installer package for multi-source video streaming workflows.',
            'is_active' => true,
        ]);
        $products['player'] = $this->upsertProduct('DMC-CVMS-PLAYER', [
            'parent_id' => $products['cvms']->id,
            'name' => 'CVMS Player',
            'description' => 'Customer-facing player application for reviewing archived video.',
            'is_active' => true,
        ]);
        $products['analytics'] = $this->upsertProduct('DMC-CVMS-ANALYTICS', [
            'parent_id' => $products['cvms']->id,
            'name' => 'CVMS Analytics',
            'description' => 'Analytics add-on for alerts, incident review, and video telemetry.',
            'is_active' => true,
        ]);
        $products['fieldsync'] = $this->upsertProduct('DMC-FIELDSYNC', [
            'parent_id' => $products['dmc']->id,
            'name' => 'FieldSync',
            'description' => 'Field team synchronization tools for distributed operations.',
            'is_active' => true,
        ]);
        $products['mobile'] = $this->upsertProduct('DMC-FIELDSYNC-MOBILE', [
            'parent_id' => $products['fieldsync']->id,
            'name' => 'FieldSync Mobile Client',
            'description' => 'Android client package for field operators.',
            'is_active' => true,
        ]);
        $products['gateway'] = $this->upsertProduct('DMC-FIELDSYNC-GATEWAY', [
            'parent_id' => $products['fieldsync']->id,
            'name' => 'FieldSync Gateway',
            'description' => 'Gateway service for synchronizing branch offices and field teams.',
            'is_active' => true,
        ]);
        $products['legacy'] = $this->upsertProduct('DMC-LEGACY-CONTROL', [
            'parent_id' => $products['dmc']->id,
            'name' => 'Legacy Control Panel',
            'description' => 'Inactive sample product used to verify status badges and filtering.',
            'is_active' => false,
        ]);

        $products['slp'] = $this->upsertProduct('SLP', [
            'parent_id' => null,
            'name' => 'Secure License Platform',
            'description' => 'License distribution, activation, and customer portal tooling.',
            'is_active' => true,
        ]);
        $products['license_manager'] = $this->upsertProduct('SLP-MANAGER', [
            'parent_id' => $products['slp']->id,
            'name' => 'License Manager',
            'description' => 'Admin backend for license provisioning and customer assignment.',
            'is_active' => true,
        ]);
        $products['customer_portal'] = $this->upsertProduct('SLP-CUSTOMER-PORTAL', [
            'parent_id' => $products['slp']->id,
            'name' => 'Customer Portal',
            'description' => 'Self-service customer area for license visibility and downloads.',
            'is_active' => true,
        ]);
        $products['activation_api'] = $this->upsertProduct('SLP-ACTIVATION-API', [
            'parent_id' => $products['slp']->id,
            'name' => 'Activation API',
            'description' => 'Installer-facing API package for online and offline activation.',
            'is_active' => true,
        ]);

        $products['dis'] = $this->upsertProduct('DIS', [
            'parent_id' => null,
            'name' => 'Data Integration Suite',
            'description' => 'Data movement and reporting modules for enterprise integrations.',
            'is_active' => true,
        ]);
        $products['etl'] = $this->upsertProduct('DIS-ETL', [
            'parent_id' => $products['dis']->id,
            'name' => 'Integration Agent',
            'description' => 'Background worker for scheduled imports and exports.',
            'is_active' => true,
        ]);
        $products['reports'] = $this->upsertProduct('DIS-REPORTS', [
            'parent_id' => $products['dis']->id,
            'name' => 'Report Builder',
            'description' => 'Report design and distribution add-on.',
            'is_active' => true,
        ]);
        $products['connectors'] = $this->upsertProduct('DIS-CONNECTORS', [
            'parent_id' => $products['dis']->id,
            'name' => 'Connector Pack',
            'description' => 'Optional integration connectors for third-party systems.',
            'is_active' => false,
        ]);

        return $products;
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function upsertProduct(string $code, array $attributes): Product
    {
        return Product::query()->updateOrCreate(['code' => $code], $attributes);
    }

    /**
     * @param  array<string, User>  $users
     * @param  array<string, LicenseType>  $licenseTypes
     * @param  array<string, Product>  $products
     * @return array<string, License>
     */
    private function seedLicenses(array $users, array $licenseTypes, array $products): array
    {
        $licenseData = [
            'enterprise' => [
                'key' => 'CA-DEMO-ENT-0001',
                'user' => 'customer',
                'product' => 'cvms',
                'sub_product' => 'streaming',
                'type' => 'ENTERPRISE',
                'quantity' => 10,
                'max_activations' => 5,
                'expired_date' => now()->addDays(90)->toDateString(),
            ],
            'trial' => [
                'key' => 'CA-DEMO-TRIAL-0002',
                'user' => 'customer',
                'product' => 'cvms',
                'sub_product' => 'player',
                'type' => 'TRIAL',
                'quantity' => 1,
                'max_activations' => 1,
                'expired_date' => now()->addDays(12)->toDateString(),
            ],
            'expired' => [
                'key' => 'CA-DEMO-EXPIRED-0003',
                'user' => 'customer',
                'product' => 'legacy',
                'sub_product' => null,
                'type' => 'SINGLE',
                'quantity' => 1,
                'max_activations' => 1,
                'expired_date' => now()->subDays(10)->toDateString(),
            ],
            'maintenance' => [
                'key' => 'CA-DEMO-MAINT-0004',
                'user' => 'customer',
                'product' => 'slp',
                'sub_product' => 'customer_portal',
                'type' => 'MAINTENANCE',
                'quantity' => 15,
                'max_activations' => null,
                'expired_date' => now()->addYear()->toDateString(),
            ],
            'acme_streaming' => [
                'key' => 'CA-ACME-MSVS-0005',
                'user' => 'acme_ops',
                'product' => 'cvms',
                'sub_product' => 'streaming',
                'type' => 'MULTI',
                'quantity' => 3,
                'max_activations' => 3,
                'expired_date' => now()->addDays(45)->toDateString(),
            ],
            'acme_api' => [
                'key' => 'CA-ACME-API-0006',
                'user' => 'acme_ops',
                'product' => 'slp',
                'sub_product' => 'activation_api',
                'type' => 'SITE',
                'quantity' => 25,
                'max_activations' => 10,
                'expired_date' => null,
            ],
            'nusantara_mobile' => [
                'key' => 'CA-NUSA-FIELD-0007',
                'user' => 'nusantara_dispatch',
                'product' => 'fieldsync',
                'sub_product' => 'mobile',
                'type' => 'ENTERPRISE',
                'quantity' => 40,
                'max_activations' => 20,
                'expired_date' => now()->addDays(180)->toDateString(),
            ],
            'nusantara_gateway' => [
                'key' => 'CA-NUSA-GATE-0008',
                'user' => 'nusantara_dispatch',
                'product' => 'fieldsync',
                'sub_product' => 'gateway',
                'type' => 'MULTI',
                'quantity' => 4,
                'max_activations' => 4,
                'expired_date' => now()->addDays(20)->toDateString(),
            ],
            'sagara_analytics' => [
                'key' => 'CA-SAGARA-ANALYTICS-0009',
                'user' => 'sagara_qa',
                'product' => 'cvms',
                'sub_product' => 'analytics',
                'type' => 'TRIAL',
                'quantity' => 1,
                'max_activations' => 1,
                'expired_date' => now()->addDays(7)->toDateString(),
            ],
            'sagara_etl' => [
                'key' => 'CA-SAGARA-DIS-0010',
                'user' => 'sagara_qa',
                'product' => 'dis',
                'sub_product' => 'etl',
                'type' => 'EDUCATIONAL',
                'quantity' => 5,
                'max_activations' => 5,
                'expired_date' => now()->addDays(120)->toDateString(),
            ],
            'acme_reports' => [
                'key' => 'CA-ACME-REPORTS-0011',
                'user' => 'acme_finance',
                'product' => 'dis',
                'sub_product' => 'reports',
                'type' => 'SINGLE',
                'quantity' => 1,
                'max_activations' => 1,
                'expired_date' => now()->addDays(30)->toDateString(),
            ],
            'legacy_archive' => [
                'key' => 'CA-LEGACY-ARCHIVE-0012',
                'user' => 'inactive_customer',
                'product' => 'legacy',
                'sub_product' => null,
                'type' => 'EVALUATION',
                'quantity' => 1,
                'max_activations' => 1,
                'expired_date' => now()->subDays(180)->toDateString(),
            ],
        ];

        return collect($licenseData)->mapWithKeys(function (array $license, string $key) use ($users, $licenseTypes, $products): array {
            $user = $users[$license['user']];
            $model = $this->upsertLicense($license['key'], [
                'user_id' => $user->id,
                'product_id' => $products[$license['product']]->id,
                'sub_product_id' => $license['sub_product'] ? $products[$license['sub_product']]->id : null,
                'license_type_id' => $licenseTypes[$license['type']]->id,
                'client_name' => $user->organization?->name,
                'quantity' => $license['quantity'],
                'max_activations' => $license['max_activations'],
                'expired_date' => $license['expired_date'],
            ]);

            return [$key => $model];
        })->all();
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
     * @param  array<string, User>  $users
     * @param  array<string, Product>  $products
     */
    private function seedEntitlements(array $users, array $products): void
    {
        collect([
            ['user' => 'customer', 'product' => 'streaming', 'status' => Entitlement::STATUS_ACTIVE, 'start' => now()->subDays(14), 'end' => now()->addYear(), 'download_end' => now()->addDays(60)],
            ['user' => 'customer', 'product' => 'player', 'status' => Entitlement::STATUS_ACTIVE, 'start' => now()->subDays(7), 'end' => now()->addDays(45), 'download_end' => now()->addDays(30)],
            ['user' => 'customer', 'product' => 'customer_portal', 'status' => Entitlement::STATUS_ACTIVE, 'start' => now()->subDays(30), 'end' => null, 'download_end' => null],
            ['user' => 'customer', 'product' => 'legacy', 'status' => Entitlement::STATUS_SUSPENDED, 'start' => now()->subYear(), 'end' => now()->subDays(30), 'download_end' => now()->subDays(30)],
            ['user' => 'acme_ops', 'product' => 'streaming', 'status' => Entitlement::STATUS_ACTIVE, 'start' => now()->subDays(40), 'end' => now()->addDays(120), 'download_end' => now()->addDays(45)],
            ['user' => 'acme_ops', 'product' => 'activation_api', 'status' => Entitlement::STATUS_ACTIVE, 'start' => now()->subDays(10), 'end' => null, 'download_end' => null],
            ['user' => 'acme_ops', 'product' => 'customer_portal', 'status' => Entitlement::STATUS_ACTIVE, 'start' => now()->subDays(10), 'end' => null, 'download_end' => now()->addDays(180)],
            ['user' => 'acme_finance', 'product' => 'reports', 'status' => Entitlement::STATUS_ACTIVE, 'start' => now()->subDays(3), 'end' => now()->addDays(30), 'download_end' => now()->addDays(14)],
            ['user' => 'acme_finance', 'product' => 'etl', 'status' => Entitlement::STATUS_EXPIRED, 'start' => now()->subDays(90), 'end' => now()->subDays(5), 'download_end' => now()->subDays(5)],
            ['user' => 'nusantara_dispatch', 'product' => 'mobile', 'status' => Entitlement::STATUS_ACTIVE, 'start' => now()->subDays(25), 'end' => now()->addDays(180), 'download_end' => now()->addDays(75)],
            ['user' => 'nusantara_dispatch', 'product' => 'gateway', 'status' => Entitlement::STATUS_ACTIVE, 'start' => now()->subDays(25), 'end' => now()->addDays(20), 'download_end' => now()->addDays(20)],
            ['user' => 'sagara_qa', 'product' => 'analytics', 'status' => Entitlement::STATUS_ACTIVE, 'start' => now()->subDays(2), 'end' => now()->addDays(7), 'download_end' => now()->addDays(7)],
            ['user' => 'sagara_qa', 'product' => 'etl', 'status' => Entitlement::STATUS_ACTIVE, 'start' => now()->subDays(14), 'end' => now()->addDays(120), 'download_end' => now()->addDays(45)],
            ['user' => 'sagara_trial', 'product' => 'analytics', 'status' => Entitlement::STATUS_ACTIVE, 'start' => now()->subDay(), 'end' => now()->addDays(14), 'download_end' => now()->addDays(14)],
            ['user' => 'metro_command', 'product' => 'streaming', 'status' => Entitlement::STATUS_ACTIVE, 'start' => now()->subDays(60), 'end' => now()->addDays(300), 'download_end' => now()->addDays(90)],
            ['user' => 'metro_command', 'product' => 'activation_api', 'status' => Entitlement::STATUS_ACTIVE, 'start' => now()->subDays(60), 'end' => now()->addDays(300), 'download_end' => null],
            ['user' => 'inactive_customer', 'product' => 'legacy', 'status' => Entitlement::STATUS_EXPIRED, 'start' => now()->subYears(2), 'end' => now()->subYear(), 'download_end' => now()->subYear()],
        ])->each(function (array $entitlement) use ($users, $products): void {
            Entitlement::query()->updateOrCreate(
                [
                    'user_id' => $users[$entitlement['user']]->id,
                    'product_id' => $products[$entitlement['product']]->id,
                ],
                [
                    'start_date' => $entitlement['start']->toDateString(),
                    'end_date' => $entitlement['end']?->toDateString(),
                    'download_expired_date' => $entitlement['download_end']?->toDateString(),
                    'status' => $entitlement['status'],
                ],
            );
        });
    }

    /**
     * @param  array<string, User>  $users
     * @param  array<string, Product>  $products
     * @return array<string, DownloadItem>
     */
    private function seedDownloadItems(array $users, array $products): array
    {
        $files = [
            'streaming_installer' => ['product' => 'streaming', 'user' => null, 'path' => 'downloads/demo/msvs-installer-1.0.0.exe', 'name' => 'MSVS Installer 1.0.0.exe', 'version' => '1.0.0', 'expired' => now()->addDays(60), 'active' => true],
            'streaming_patch' => ['product' => 'streaming', 'user' => null, 'path' => 'downloads/demo/msvs-patch-1.1.0.exe', 'name' => 'MSVS Patch 1.1.0.exe', 'version' => '1.1.0', 'expired' => now()->addDays(90), 'active' => true],
            'player' => ['product' => 'player', 'user' => null, 'path' => 'downloads/demo/cvms-player-2.3.0.exe', 'name' => 'CVMS Player 2.3.0.exe', 'version' => '2.3.0', 'expired' => now()->addDays(30), 'active' => true],
            'analytics_trial' => ['product' => 'analytics', 'user' => 'sagara_qa', 'path' => 'downloads/demo/sagara-analytics-trial-0.8.0.zip', 'name' => 'Sagara Analytics Trial 0.8.0.zip', 'version' => '0.8.0', 'expired' => now()->addDays(7), 'active' => true],
            'acme_hotfix' => ['product' => 'streaming', 'user' => 'customer', 'path' => 'downloads/demo/acme-msvs-hotfix-1.0.1.exe', 'name' => 'ACME MSVS Hotfix 1.0.1.exe', 'version' => '1.0.1', 'expired' => now()->addDays(14), 'active' => true],
            'fieldsync_mobile' => ['product' => 'mobile', 'user' => null, 'path' => 'downloads/demo/fieldsync-mobile-3.2.0.apk', 'name' => 'FieldSync Mobile 3.2.0.apk', 'version' => '3.2.0', 'expired' => now()->addDays(75), 'active' => true],
            'fieldsync_gateway' => ['product' => 'gateway', 'user' => null, 'path' => 'downloads/demo/fieldsync-gateway-3.2.0.zip', 'name' => 'FieldSync Gateway 3.2.0.zip', 'version' => '3.2.0', 'expired' => now()->addDays(45), 'active' => true],
            'license_manager' => ['product' => 'license_manager', 'user' => null, 'path' => 'downloads/demo/license-manager-server-4.0.0.zip', 'name' => 'License Manager Server 4.0.0.zip', 'version' => '4.0.0', 'expired' => null, 'active' => true],
            'customer_portal' => ['product' => 'customer_portal', 'user' => null, 'path' => 'downloads/demo/customer-portal-4.0.0.zip', 'name' => 'Customer Portal 4.0.0.zip', 'version' => '4.0.0', 'expired' => null, 'active' => true],
            'activation_sdk' => ['product' => 'activation_api', 'user' => null, 'path' => 'downloads/demo/activation-api-sdk-4.1.0.zip', 'name' => 'Activation API SDK 4.1.0.zip', 'version' => '4.1.0', 'expired' => null, 'active' => true],
            'etl_agent' => ['product' => 'etl', 'user' => null, 'path' => 'downloads/demo/integration-agent-5.0.0.msi', 'name' => 'Integration Agent 5.0.0.msi', 'version' => '5.0.0', 'expired' => now()->addDays(45), 'active' => true],
            'report_builder' => ['product' => 'reports', 'user' => null, 'path' => 'downloads/demo/report-builder-5.0.0.exe', 'name' => 'Report Builder 5.0.0.exe', 'version' => '5.0.0', 'expired' => now()->addDays(30), 'active' => true],
            'expired_beta' => ['product' => 'streaming', 'user' => null, 'path' => 'downloads/demo/msvs-beta-0.9.0.exe', 'name' => 'MSVS Beta 0.9.0.exe', 'version' => '0.9.0', 'expired' => now()->subDays(5), 'active' => true],
            'inactive_legacy' => ['product' => 'legacy', 'user' => null, 'path' => 'downloads/demo/legacy-control-panel-0.9.0.exe', 'name' => 'Legacy Control Panel 0.9.0.exe', 'version' => '0.9.0', 'expired' => now()->subDays(30), 'active' => false],
        ];

        return collect($files)->mapWithKeys(function (array $file, string $key) use ($users, $products): array {
            Storage::disk('local')->put(
                $file['path'],
                "Demo file placeholder for {$file['name']}.\n",
            );

            $downloadItem = DownloadItem::query()->updateOrCreate(
                ['file_path' => $file['path']],
                [
                    'product_id' => $products[$file['product']]->id,
                    'user_id' => $file['user'] ? $users[$file['user']]->id : null,
                    'file_name' => $file['name'],
                    'file_size' => Storage::disk('local')->size($file['path']),
                    'version' => $file['version'],
                    'expired_date' => $file['expired']?->toDateString(),
                    'is_active' => $file['active'],
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
        collect([
            ['license' => 'enterprise', 'device' => 'ACME-LAPTOP-001', 'host' => 'acme-video-ops-01', 'ip' => '203.0.113.10', 'location' => 'Jakarta HQ', 'status' => LicenseActivation::STATUS_ACTIVE],
            ['license' => 'enterprise', 'device' => 'ACME-LAPTOP-002', 'host' => 'acme-video-ops-02', 'ip' => '203.0.113.11', 'location' => 'Jakarta HQ', 'status' => LicenseActivation::STATUS_ACTIVE],
            ['license' => 'enterprise', 'device' => 'ACME-ROOM-CTRL-01', 'host' => 'acme-control-room', 'ip' => '203.0.113.12', 'location' => 'Operations Center', 'status' => LicenseActivation::STATUS_ACTIVE],
            ['license' => 'enterprise', 'device' => 'ACME-DR-LAPTOP-01', 'host' => 'acme-dr-workstation', 'ip' => '203.0.113.13', 'location' => 'Disaster Recovery Site', 'status' => LicenseActivation::STATUS_INACTIVE],
            ['license' => 'trial', 'device' => 'ACME-TRIAL-VM-01', 'host' => 'acme-trial-vm', 'ip' => '203.0.113.14', 'location' => 'QA Lab', 'status' => LicenseActivation::STATUS_ACTIVE],
            ['license' => 'expired', 'device' => 'ACME-LEGACY-01', 'host' => 'acme-legacy-panel', 'ip' => '203.0.113.15', 'location' => 'Archive', 'status' => LicenseActivation::STATUS_INACTIVE],
            ['license' => 'maintenance', 'device' => 'ACME-PORTAL-01', 'host' => 'portal-app-01', 'ip' => '203.0.113.16', 'location' => 'Jakarta DC', 'status' => LicenseActivation::STATUS_ACTIVE],
            ['license' => 'maintenance', 'device' => 'ACME-PORTAL-02', 'host' => 'portal-app-02', 'ip' => '203.0.113.17', 'location' => 'Jakarta DC', 'status' => LicenseActivation::STATUS_ACTIVE],
            ['license' => 'acme_streaming', 'device' => 'ACME-OPS-STREAM-01', 'host' => 'stream-ops-01', 'ip' => '198.51.100.10', 'location' => 'NOC', 'status' => LicenseActivation::STATUS_ACTIVE],
            ['license' => 'acme_streaming', 'device' => 'ACME-OPS-STREAM-02', 'host' => 'stream-ops-02', 'ip' => '198.51.100.11', 'location' => 'NOC', 'status' => LicenseActivation::STATUS_ACTIVE],
            ['license' => 'acme_streaming', 'device' => 'ACME-OPS-STREAM-03', 'host' => 'stream-ops-03', 'ip' => '198.51.100.12', 'location' => 'NOC', 'status' => LicenseActivation::STATUS_ACTIVE],
            ['license' => 'acme_api', 'device' => 'ACME-ACTIVATION-01', 'host' => 'activation-api-01', 'ip' => '198.51.100.13', 'location' => 'Jakarta DC', 'status' => LicenseActivation::STATUS_ACTIVE],
            ['license' => 'acme_api', 'device' => 'ACME-ACTIVATION-02', 'host' => 'activation-api-02', 'ip' => '198.51.100.14', 'location' => 'Jakarta DC', 'status' => LicenseActivation::STATUS_ACTIVE],
            ['license' => 'nusantara_mobile', 'device' => 'NUSA-TABLET-001', 'host' => 'nusa-tablet-001', 'ip' => '192.0.2.10', 'location' => 'Surabaya Depot', 'status' => LicenseActivation::STATUS_ACTIVE],
            ['license' => 'nusantara_mobile', 'device' => 'NUSA-TABLET-002', 'host' => 'nusa-tablet-002', 'ip' => '192.0.2.11', 'location' => 'Semarang Hub', 'status' => LicenseActivation::STATUS_ACTIVE],
            ['license' => 'nusantara_mobile', 'device' => 'NUSA-TABLET-003', 'host' => 'nusa-tablet-003', 'ip' => '192.0.2.12', 'location' => 'Malang Hub', 'status' => LicenseActivation::STATUS_ACTIVE],
            ['license' => 'nusantara_gateway', 'device' => 'NUSA-GATEWAY-01', 'host' => 'nusa-gateway-east', 'ip' => '192.0.2.13', 'location' => 'East Java', 'status' => LicenseActivation::STATUS_ACTIVE],
            ['license' => 'sagara_analytics', 'device' => 'SAGARA-QA-01', 'host' => 'sagara-qa-notebook', 'ip' => '192.0.2.30', 'location' => 'Bandung Lab', 'status' => LicenseActivation::STATUS_ACTIVE],
            ['license' => 'sagara_etl', 'device' => 'SAGARA-ETL-01', 'host' => 'sagara-etl-worker-01', 'ip' => '192.0.2.31', 'location' => 'Bandung DC', 'status' => LicenseActivation::STATUS_ACTIVE],
            ['license' => 'acme_reports', 'device' => 'ACME-FINANCE-01', 'host' => 'acme-finance-laptop', 'ip' => '198.51.100.40', 'location' => 'Finance Office', 'status' => LicenseActivation::STATUS_ACTIVE],
            ['license' => 'legacy_archive', 'device' => 'LEGACY-ARCHIVE-01', 'host' => 'legacy-archive-vm', 'ip' => '203.0.113.90', 'location' => 'Archive', 'status' => LicenseActivation::STATUS_INACTIVE],
        ])->each(function (array $activation) use ($licenses): void {
            LicenseActivation::query()->updateOrCreate(
                [
                    'license_id' => $licenses[$activation['license']]->id,
                    'device_id' => $activation['device'],
                ],
                [
                    'hostname' => $activation['host'],
                    'ip_address' => $activation['ip'],
                    'location' => $activation['location'],
                    'status' => $activation['status'],
                ],
            );
        });
    }

    /**
     * @param  array<string, User>  $users
     * @param  array<string, DownloadItem>  $downloadItems
     */
    private function seedDownloadLogs(array $users, array $downloadItems): void
    {
        collect([
            ['user' => 'customer', 'item' => 'streaming_installer', 'ip' => '203.0.113.10', 'time' => now()->subHours(3)],
            ['user' => 'customer', 'item' => 'player', 'ip' => '203.0.113.11', 'time' => now()->subDays(1)->subHours(2)],
            ['user' => 'customer', 'item' => 'acme_hotfix', 'ip' => '203.0.113.12', 'time' => now()->subDays(2)],
            ['user' => 'acme_ops', 'item' => 'streaming_patch', 'ip' => '198.51.100.10', 'time' => now()->subMinutes(45)],
            ['user' => 'acme_ops', 'item' => 'activation_sdk', 'ip' => '198.51.100.11', 'time' => now()->subDays(3)],
            ['user' => 'acme_ops', 'item' => 'customer_portal', 'ip' => '198.51.100.12', 'time' => now()->subDays(5)],
            ['user' => 'acme_finance', 'item' => 'report_builder', 'ip' => '198.51.100.40', 'time' => now()->subHours(8)],
            ['user' => 'nusantara_dispatch', 'item' => 'fieldsync_mobile', 'ip' => '192.0.2.10', 'time' => now()->subHours(5)],
            ['user' => 'nusantara_dispatch', 'item' => 'fieldsync_gateway', 'ip' => '192.0.2.13', 'time' => now()->subDays(1)],
            ['user' => 'sagara_qa', 'item' => 'analytics_trial', 'ip' => '192.0.2.30', 'time' => now()->subMinutes(20)],
            ['user' => 'sagara_qa', 'item' => 'etl_agent', 'ip' => '192.0.2.31', 'time' => now()->subDays(4)],
            ['user' => 'metro_command', 'item' => 'streaming_installer', 'ip' => '203.0.113.50', 'time' => now()->subDays(6)],
            ['user' => 'metro_command', 'item' => 'activation_sdk', 'ip' => '203.0.113.51', 'time' => now()->subDays(6)->subHours(4)],
            ['user' => 'inactive_customer', 'item' => 'inactive_legacy', 'ip' => '203.0.113.90', 'time' => now()->subMonths(8)],
        ])->each(function (array $log) use ($users, $downloadItems): void {
            DownloadLog::query()->updateOrCreate(
                [
                    'user_id' => $users[$log['user']]->id,
                    'download_item_id' => $downloadItems[$log['item']]->id,
                    'ip_address' => $log['ip'],
                ],
                [
                    'downloaded_at' => $log['time'],
                ],
            );
        });
    }
}
