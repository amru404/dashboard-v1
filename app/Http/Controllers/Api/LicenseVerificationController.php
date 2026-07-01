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
     * Verify a license key and return its details
     * 
     * POST /api/license/verify
     * Body: { "license_key": "XXXX-XXXX-XXXX-XXXX", "device_fingerprint": "optional" }
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
        
        // Check activation limit
        $canActivate = $license->canActivateDevice($deviceFingerprint);
        $hasActiveDevice = $deviceFingerprint ? $license->hasActiveActivationForDevice($deviceFingerprint) : false;

        // Get owner information
        $owner = $license->users->first();

        return response()->json([
            'valid' => !$isExpired,
            'license' => [
                'id' => $license->id,
                'license_key' => $license->license_key,
                'status' => $isExpired ? 'expired' : 'active',
                'expired_date' => $license->expired_date?->format('Y-m-d'),
                'days_until_expiry' => $license->daysUntilExpiry(),
                'product' => [
                    'id' => $license->product->id,
                    'name' => $license->product->name,
                    'code' => $license->product->code,
                    'version' => $license->product->version,
                ],
                'sub_product' => $license->subProduct ? [
                    'id' => $license->subProduct->id,
                    'name' => $license->subProduct->name,
                    'code' => $license->subProduct->code,
                    'version' => $license->subProduct->version,
                ] : null,
                'license_type' => [
                    'id' => $license->licenseType->id,
                    'name' => $license->licenseType->name,
                    'code' => $license->licenseType->code,
                ],
                'owner' => $owner ? [
                    'name' => $owner->name,
                    'email' => $owner->email,
                    'organization' => $owner->organization?->name,
                ] : null,
                'activations' => [
                    'total' => $license->activations_count,
                    'active' => $license->active_activations_count,
                    'max' => $license->max_activations,
                    'remaining' => $license->remainingActivations(),
                    'can_activate' => $canActivate,
                    'has_active_device' => $hasActiveDevice,
                ],
            ],
        ]);
    }

    /**
     * Activate a license for a device
     * 
     * POST /api/license/activate
     * Body: { "license_key": "XXXX-XXXX", "device_fingerprint": "abc123", "hostname": "MY-PC" }
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
            ->with(['product', 'subProduct'])
            ->first();

        if (!$license) {
            return response()->json([
                'success' => false,
                'error' => 'License not found',
            ], 404);
        }

        // Check if license is expired
        if ($license->isExpired()) {
            return response()->json([
                'success' => false,
                'error' => 'License has expired',
                'expired_date' => $license->expired_date->format('Y-m-d'),
            ], 403);
        }

        // Check if device already has active activation
        if ($license->hasActiveActivationForDevice($deviceFingerprint)) {
            $activation = $license->activations()
                ->where('device_id', LicenseActivation::normalizeDeviceId($deviceFingerprint))
                ->where('status', LicenseActivation::STATUS_ACTIVE)
                ->first();

            return response()->json([
                'success' => true,
                'message' => 'Device already activated',
                'activation' => [
                    'id' => $activation->id,
                    'device_id' => $activation->device_id,
                    'hostname' => $activation->hostname,
                    'activated_at' => $activation->activated_at?->toIso8601String(),
                    'status' => $activation->status,
                ],
            ]);
        }

        // Check if can activate (activation limit)
        if (!$license->canActivateDevice($deviceFingerprint)) {
            return response()->json([
                'success' => false,
                'error' => 'Activation limit reached',
                'max_activations' => $license->max_activations,
                'active_activations' => $license->activeActivationCount(),
            ], 403);
        }

        // Create new activation
        $activation = $license->activations()->create([
            'device_id' => $deviceFingerprint,
            'hostname' => $hostname,
            'activated_at' => now(),
            'status' => LicenseActivation::STATUS_ACTIVE,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'License activated successfully',
            'activation' => [
                'id' => $activation->id,
                'device_id' => $activation->device_id,
                'hostname' => $activation->hostname,
                'activated_at' => $activation->activated_at?->toIso8601String(),
                'status' => $activation->status,
            ],
            'license' => [
                'product' => $license->product->name,
                'sub_product' => $license->subProduct?->name,
                'expired_date' => $license->expired_date?->format('Y-m-d'),
                'remaining_activations' => $license->remainingActivations(),
            ],
        ], 201);
    }

    /**
     * Get all products and licenses for a user by email
     * 
     * POST /api/user/products
     * Body: { "email": "user@example.com" }
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
            ->withCount([
                'activations',
                'activeActivations as active_activations_count',
            ])
            ->get();

        // Group by product
        $productGroups = $licenses->groupBy('product_id')->map(function ($licensesGroup) {
            $product = $licensesGroup->first()->product;
            
            return [
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'code' => $product->code,
                    'version' => $product->version,
                    'description' => $product->description,
                ],
                'licenses' => $licensesGroup->map(function ($license) {
                    return [
                        'id' => $license->id,
                        'license_key' => $license->masked_license_key,
                        'status' => $license->isExpired() ? 'expired' : 'active',
                        'expired_date' => $license->expired_date?->format('Y-m-d'),
                        'days_until_expiry' => $license->daysUntilExpiry(),
                        'sub_product' => $license->subProduct ? [
                            'id' => $license->subProduct->id,
                            'name' => $license->subProduct->name,
                            'code' => $license->subProduct->code,
                        ] : null,
                        'license_type' => [
                            'id' => $license->licenseType->id,
                            'name' => $license->licenseType->name,
                        ],
                        'activations' => [
                            'total' => $license->activations_count,
                            'active' => $license->active_activations_count,
                            'max' => $license->max_activations,
                            'remaining' => $license->remainingActivations(),
                        ],
                    ];
                })->values(),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'organization' => $user->organization?->name,
            ],
            'products' => $productGroups,
            'total_licenses' => $licenses->count(),
        ]);
    }

    /**
     * Get all licenses for a specific product by product code
     * 
     * POST /api/product/licenses
     * Body: { "product_code": "PROD-001", "email": "user@example.com" }
     */
    public function productLicenses(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_code' => ['required', 'string'],
            'email' => ['nullable', 'email'],
        ]);

        // Find product
        $product = Product::where('code', $validated['product_code'])
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'error' => 'Product not found',
            ], 404);
        }

        // Build query for licenses
        $query = License::where('product_id', $product->id)
            ->with([
                'subProduct',
                'licenseType',
                'users' => fn($q) => $q->wherePivot('is_owner', true)->with('organization'),
            ])
            ->withCount([
                'activations',
                'activeActivations as active_activations_count',
            ]);

        // Filter by user email if provided
        if (!empty($validated['email'])) {
            $user = User::where('email', $validated['email'])->first();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error' => 'User not found',
                ], 404);
            }

            // Only get licenses accessible by this user
            $query->whereHas('users', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        $licenses = $query->get();

        return response()->json([
            'success' => true,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'code' => $product->code,
                'version' => $product->version,
                'description' => $product->description,
            ],
            'licenses' => $licenses->map(function ($license) {
                $owner = $license->users->first();
                
                return [
                    'id' => $license->id,
                    'license_key' => $license->masked_license_key,
                    'status' => $license->isExpired() ? 'expired' : 'active',
                    'expired_date' => $license->expired_date?->format('Y-m-d'),
                    'days_until_expiry' => $license->daysUntilExpiry(),
                    'sub_product' => $license->subProduct ? [
                        'id' => $license->subProduct->id,
                        'name' => $license->subProduct->name,
                        'code' => $license->subProduct->code,
                    ] : null,
                    'license_type' => [
                        'id' => $license->licenseType->id,
                        'name' => $license->licenseType->name,
                    ],
                    'owner' => $owner ? [
                        'name' => $owner->name,
                        'email' => $owner->email,
                        'organization' => $owner->organization?->name,
                    ] : null,
                    'activations' => [
                        'total' => $license->activations_count,
                        'active' => $license->active_activations_count,
                        'max' => $license->max_activations,
                        'remaining' => $license->remainingActivations(),
                    ],
                ];
            })->values(),
            'total_licenses' => $licenses->count(),
        ]);
    }
}
