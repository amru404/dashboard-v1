<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Models\LicenseActivation;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LicenseVerificationController extends Controller
{
    /**
     * Build product path breadcrumb
     */
    private function getProductPath(Product $product): array
    {
        $path = [];
        $current = $product;

        while ($current) {
            array_unshift($path, $current->name);
            $current = $current->parent;
        }

        return $path;
    }

    /**
     * Get location from IP address (stub - can integrate with geoip service)
     */
    private function getLocationFromIP(string $ip): ?string
    {
        // Placeholder for geoip lookup
        // Can integrate with services like https://ip-api.com/ or similar
        return null;
    }

    /**
     * Get active activation for device
     */
    private function getActiveActivationForDevice(License $license, ?string $deviceFingerprint): ?array
    {
        if (!$deviceFingerprint) {
            return null;
        }

        $activation = $license->activations()
            ->where('device_id', LicenseActivation::normalizeDeviceId($deviceFingerprint))
            ->where('status', LicenseActivation::STATUS_ACTIVE)
            ->first();

        if (!$activation) {
            return null;
        }

        return [
            'hostname' => $activation->hostname,
            'location' => $activation->location,
            'ip_address' => $activation->ip_address,
            'activated_at' => $activation->activated_at?->toIso8601String(),
        ];
    }

    /**
     * Verify a license key and return its details
     * 
     * POST /api/license/verify
     */
    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'license_key' => ['required', 'string'],
            'device_fingerprint' => ['nullable', 'string'],
        ]);

        $licenseKey = $request->input('license_key');
        $deviceFingerprint = $request->input('device_fingerprint');

        // Find license by key
        $license = License::whereLicenseKey($licenseKey)
            ->with([
                'product',
                'subProduct',
                'licenseType',
                'users' => fn($q) => $q->wherePivot('is_owner', true),
            ])
            ->withCount([
                'activations',
                'activeActivations as active_activations_count',
            ])
            ->first();

        if (!$license) {
            return response()->json([
                'valid' => false,
                'error' => 'License not found',
            ], 404);
        }

        // Check if license is expired
        $isExpired = $license->isExpired();
        $isActivatedOnDevice = $deviceFingerprint ? $license->hasActiveActivationForDevice($deviceFingerprint) : false;
        $canActivate = $license->canActivateDevice($deviceFingerprint);

        // Get owner information
        $owner = $license->users->first();

        // Build product path
        $productPath = $this->getProductPath($license->product);
        if ($license->subProduct) {
            $productPath[] = $license->subProduct->name;
        }

        return response()->json([
            'valid' => !$isExpired,
            'product_name' => $license->product->name,
            'product_code' => $license->product->code,
            'sub_product_name' => $license->subProduct?->name,
            'sub_product_code' => $license->subProduct?->code,
            'product_path' => $productPath,
            'license_key' => $license->license_key,
            'license_type' => $license->licenseType->name,
            'organization_name' => $owner?->organization?->name,
            'quantity' => $license->quantity,
            'max_activations' => $license->max_activations,
            'expired_date' => $license->expired_date?->format('Y-m-d'),
            'is_expired' => $isExpired,
            'is_activated' => $license->active_activations_count > 0,
            'is_activated_on_this_device' => $isActivatedOnDevice,
            'active_count' => $license->active_activations_count,
            'can_activate' => $canActivate,
            'activation' => $this->getActiveActivationForDevice($license, $deviceFingerprint),
        ]);
    }

    /**
     * Activate a license for a device
     * 
     * POST /api/license/activate
     */
    public function activate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'license_key' => ['required', 'string'],
            'device_fingerprint' => ['required', 'string'],
            'hostname' => ['nullable', 'string', 'max:255'],
        ]);

        $licenseKey = $validated['license_key'];
        $deviceFingerprint = $validated['device_fingerprint'];
        $hostname = $validated['hostname'] ?? null;

        // Find license
        $license = License::whereLicenseKey($licenseKey)
            ->with(['product', 'subProduct', 'licenseType', 'users' => fn($q) => $q->wherePivot('is_owner', true)])
            ->withCount(['activeActivations as active_activations_count'])
            ->first();

        if (!$license) {
            return response()->json([
                'valid' => false,
                'error' => 'License not found',
            ], 404);
        }

        // Check if license is expired
        if ($license->isExpired()) {
            return response()->json([
                'valid' => false,
                'error' => 'License has expired',
                'expired_date' => $license->expired_date->format('Y-m-d'),
            ], 403);
        }

        $owner = $license->users->first();
        $productPath = $this->getProductPath($license->product);
        if ($license->subProduct) {
            $productPath[] = $license->subProduct->name;
        }

        // Check if device already has active activation
        if ($license->hasActiveActivationForDevice($deviceFingerprint)) {
            $activation = $license->activations()
                ->where('device_id', LicenseActivation::normalizeDeviceId($deviceFingerprint))
                ->where('status', LicenseActivation::STATUS_ACTIVE)
                ->first();

            return response()->json([
                'valid' => true,
                'activated' => true,
                'product_name' => $license->product->name,
                'product_code' => $license->product->code,
                'sub_product_name' => $license->subProduct?->name,
                'sub_product_code' => $license->subProduct?->code,
                'product_path' => $productPath,
                'license_key' => $license->license_key,
                'organization_name' => $owner?->organization?->name,
                'activation' => [
                    'hostname' => $activation->hostname,
                    'location' => $activation->location,
                    'ip_address' => $activation->ip_address,
                    'activated_at' => $activation->activated_at?->toIso8601String(),
                ],
            ]);
        }

        // Check if can activate (activation limit)
        if (!$license->canActivateDevice($deviceFingerprint)) {
            return response()->json([
                'valid' => false,
                'error' => 'Activation limit reached',
                'max_activations' => $license->max_activations,
                'active_count' => $license->active_activations_count,
            ], 403);
        }

        // Get client IP and location info
        $ipAddress = $request->ip();
        $location = $this->getLocationFromIP($ipAddress);

        // Create new activation
        $activation = $license->activations()->create([
            'device_id' => $deviceFingerprint,
            'hostname' => $hostname,
            'ip_address' => $ipAddress,
            'location' => $location,
            'activated_at' => now(),
            'status' => LicenseActivation::STATUS_ACTIVE,
        ]);

        return response()->json([
            'valid' => true,
            'activated' => true,
            'product_name' => $license->product->name,
            'product_code' => $license->product->code,
            'sub_product_name' => $license->subProduct?->name,
            'sub_product_code' => $license->subProduct?->code,
            'product_path' => $productPath,
            'license_key' => $license->license_key,
            'organization_name' => $owner?->organization?->name,
            'activation' => [
                'hostname' => $activation->hostname,
                'location' => $activation->location,
                'ip_address' => $activation->ip_address,
                'activated_at' => $activation->activated_at?->toIso8601String(),
            ],
        ], 201);
    }

    /**
     * Get all products and licenses for a user by email
     * 
     * POST /api/user/products
     */
    public function userProducts(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Find user by email
        $user = User::where('email', $validated['email'])
            ->with('organization')
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'User not found',
            ], 404);
        }

        // Get all accessible licenses (owned + shared) via pivot
        $licenses = $user->accessibleLicenses()
            ->with([
                'product',
                'subProduct',
                'licenseType',
            ])
            ->get();

        // Build product tree structure
        $productTree = $this->buildProductTree($licenses, $user);

        return response()->json([
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'organization' => $user->organization?->name,
            ],
            'products' => $productTree,
        ]);
    }

    /**
     * Build product tree with licenses
     */
    private function buildProductTree($licenses, User $user): array
    {
        $products = [];

        // Get all unique products
        $productIds = $licenses->pluck('product_id')->unique();
        $rootProducts = Product::whereIn('id', $productIds)
            ->whereNull('parent_id')
            ->with(['allChildren', 'licenses'])
            ->get();

        foreach ($rootProducts as $product) {
            $productData = $this->formatProductWithTree($product, $licenses, $user);
            if ($productData) {
                $products[] = $productData;
            }
        }

        return $products;
    }

    /**
     * Format product with tree structure
     */
    private function formatProductWithTree(Product $product, $allLicenses, User $user): ?array
    {
        // Get licenses for this product
        $productLicenses = $allLicenses->where('product_id', $product->id);
        
        // Get children with licenses
        $children = [];
        foreach ($product->children as $child) {
            $childData = $this->formatProductChildWithTree($child, $allLicenses, $user);
            if ($childData) {
                $children[] = $childData;
            }
        }

        // Build response
        $response = [
            'product_name' => $product->name,
            'product_code' => $product->code,
            'is_parent' => true,
        ];

        // Add license info if exists
        if ($productLicenses->isNotEmpty()) {
            $license = $productLicenses->first();
            $response['entitlement'] = [
                'status' => 'active', // Can add entitlement logic if available
                'start_date' => $license->created_at?->format('Y-m-d'),
                'end_date' => null,
                'download_expired_date' => $license->expired_date?->format('Y-m-d'),
            ];
            $response['license'] = [
                'license_key' => $license->license_key,
                'is_parent_only' => $license->sub_product_id === null,
                'license_type' => $license->licenseType->name,
                'quantity' => $license->quantity,
                'max_activations' => $license->max_activations,
                'expired_date' => $license->expired_date?->format('Y-m-d'),
                'is_expired' => $license->isExpired(),
                'active_count' => $license->activeActivationCount(),
            ];
        }

        if (!empty($children)) {
            $response['children'] = $children;
        } else {
            $response['children'] = [];
        }

        return $response;
    }

    /**
     * Format product child with tree
     */
    private function formatProductChildWithTree(Product $product, $allLicenses, User $user): ?array
    {
        $productLicenses = $allLicenses->where('product_id', $product->id)->values();
        
        $children = [];
        foreach ($product->children as $child) {
            $childData = $this->formatProductChildWithTree($child, $allLicenses, $user);
            if ($childData) {
                $children[] = $childData;
            }
        }

        $response = [
            'product_name' => $product->name,
            'product_code' => $product->code,
            'is_sub_product' => true,
            'parent_product_code' => $product->parent?->code,
            'licenses' => $productLicenses->map(function ($license) {
                return [
                    'license_key' => $license->license_key,
                    'license_type' => $license->licenseType->name,
                    'quantity' => $license->quantity,
                    'max_activations' => $license->max_activations,
                    'expired_date' => $license->expired_date?->format('Y-m-d'),
                    'is_expired' => $license->isExpired(),
                    'active_count' => $license->activeActivationCount(),
                ];
            })->values()->toArray(),
            'children' => $children,
        ];

        return $response;
    }

    /**
     * Get all licenses for a specific product by product code
     * 
     * POST /api/product/licenses
     */
    public function productLicenses(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_code' => ['required', 'string'],
            'email' => ['nullable', 'email'],
        ]);

        // Find product
        $product = Product::where('code', $validated['product_code'])
            ->with(['parent', 'children'])
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'error' => 'Product not found',
            ], 404);
        }

        // Get licenses for THIS SPECIFIC PRODUCT ONLY (not sub_product_id)
        $query = License::where('product_id', $product->id)
            ->whereNull('sub_product_id') // Only licenses where this is the parent product
            ->with(['licenseType', 'users' => fn($q) => $q->wherePivot('is_owner', true)]);

        // Filter by user if provided
        if (!empty($validated['email'])) {
            $user = User::where('email', $validated['email'])->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error' => 'User not found',
                ], 404);
            }

            $query->whereHas('users', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        $licenses = $query->get();

        // Build response with children
        $response = [
            'product_name' => $product->name,
            'product_code' => $product->code,
            'licenses' => $licenses->map(function ($license) {
                $owner = $license->users->first();
                return [
                    'license_key' => $license->license_key,
                    'is_parent_only' => $license->sub_product_id === null,
                    'license_type' => $license->licenseType->name,
                    'user_name' => $owner?->name,
                    'user_email' => $owner?->email,
                    'organization' => $owner?->organization?->name,
                    'quantity' => $license->quantity,
                    'max_activations' => $license->max_activations,
                    'expired_date' => $license->expired_date?->format('Y-m-d'),
                    'is_expired' => $license->isExpired(),
                    'active_count' => $license->activeActivationCount(),
                ];
            })->values()->toArray(),
        ];

        // Add children recursively
        if ($product->children->isNotEmpty()) {
            $response['children'] = $this->buildChildrenTree($product->children, $validated['email'] ?? null);
        } else {
            $response['children'] = [];
        }

        // Calculate total licenses across all levels and place it after product_code
        $totalLicenses = $this->countAllLicenses($response);
        $response = array_merge(
            array_slice($response, 0, 2), // product_name, product_code
            ['total_licenses' => $totalLicenses],
            array_slice($response, 2) // rest of the data
        );

        return response()->json($response);
    }

    /**
     * Count all licenses recursively (parent + all children)
     */
    private function countAllLicenses(array $data): int
    {
        $count = count($data['licenses'] ?? []);

        if (!empty($data['children'])) {
            foreach ($data['children'] as $child) {
                $count += $this->countAllLicenses($child);
            }
        }

        return $count;
    }

    /**
     * Build children tree for product licenses endpoint
     */
    private function buildChildrenTree($children, ?string $email = null): array
    {
        $result = [];

        foreach ($children as $child) {
            // Get licenses where this child is the sub_product (sub_product_id = $child->id)
            $query = License::where('sub_product_id', $child->id)
                ->with(['licenseType', 'users' => fn($q) => $q->wherePivot('is_owner', true)]);

            if ($email) {
                $user = User::where('email', $email)->first();
                if ($user) {
                    $query->whereHas('users', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    });
                }
            }

            $licenses = $query->get();

            $childData = [
                'product_name' => $child->name,
                'product_code' => $child->code,
                'is_sub_product' => true,
                'parent_product_code' => $child->parent?->code,
                'licenses' => $licenses->map(function ($license) {
                    $owner = $license->users->first();
                    return [
                        'license_key' => $license->license_key,
                        'is_parent_only' => $license->sub_product_id === null,
                        'license_type' => $license->licenseType->name,
                        'user_name' => $owner?->name,
                        'user_email' => $owner?->email,
                        'organization' => $owner?->organization?->name,
                        'quantity' => $license->quantity,
                        'max_activations' => $license->max_activations,
                        'expired_date' => $license->expired_date?->format('Y-m-d'),
                        'is_expired' => $license->isExpired(),
                        'active_count' => $license->activeActivationCount(),
                    ];
                })->values()->toArray(),
            ];

            if ($child->children->isNotEmpty()) {
                $childData['children'] = $this->buildChildrenTree($child->children, $email);
            } else {
                $childData['children'] = [];
            }

            $result[] = $childData;
        }

        return $result;
    }
}
