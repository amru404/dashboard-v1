<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Models\LicenseActivation;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LicenseController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        // Use accessibleLicenses instead of licenses() to include shared licenses
        $licensedProductIds = $user->accessibleLicenses()
            ->pluck('product_id')
            ->merge($user->accessibleLicenses()->whereNotNull('sub_product_id')->pluck('sub_product_id'))
            ->filter()
            ->unique()
            ->values();

        $allProductIds = $licensedProductIds->values();
        $currentParentIds = $allProductIds;

        while ($currentParentIds->isNotEmpty()) {
            $parentIds = Product::query()
                ->whereIn('id', $currentParentIds)
                ->pluck('parent_id')
                ->filter()
                ->unique();

            $newParentIds = $parentIds->diff($allProductIds);
            if ($newParentIds->isEmpty()) {
                break;
            }

            $allProductIds = $allProductIds->merge($newParentIds)->unique();
            $currentParentIds = $newParentIds;
        }

        $rootProductIds = Product::query()
            ->whereIn('id', $allProductIds)
            ->whereNull('parent_id')
            ->pluck('id');

        $rootProducts = Product::query()
            ->whereIn('id', $rootProductIds)
            ->with([
                'licenses.users' => fn ($query) => $query->where('user_id', $user->id)->withPivot('is_owner'),
                'licenses.licenseType',
                'allChildren.licenses.users' => fn ($query) => $query->where('user_id', $user->id)->withPivot('is_owner'),
                'allChildren.licenses.licenseType',
            ])
            ->orderBy('name')
            ->paginate(12);

        // Load active activations count for all licenses via withCount
        // This is more efficient than loading all activations
        $allLicenseIds = [];
        $rootProducts->each(function ($product) use (&$allLicenseIds) {
            $allLicenseIds = array_merge($allLicenseIds, $product->licenses->pluck('id')->toArray());
            $product->allChildren->each(function ($child) use (&$allLicenseIds) {
                $allLicenseIds = array_merge($allLicenseIds, $child->licenses->pluck('id')->toArray());
            });
        });

        if (!empty($allLicenseIds)) {
            $licenseCounts = License::whereIn('id', $allLicenseIds)
                ->withCount([
                    'activeActivations as active_activations_count'
                ])
                ->get()
                ->keyBy('id');

            // Attach the counts to licenses
            $rootProducts->each(function ($product) use ($licenseCounts, $user) {
                $product->setRelation('licenses', $product->licenses->filter(function ($license) use ($user, $licenseCounts) {
                    if ($license->users->contains('id', $user->id)) {
                        if (isset($licenseCounts[$license->id])) {
                            $license->active_activations_count = $licenseCounts[$license->id]->active_activations_count;
                        }
                        return true;
                    }
                    return false;
                }));
                
                $product->allChildren->each(function ($child) use ($licenseCounts, $user) {
                    $child->setRelation('licenses', $child->licenses->filter(function ($license) use ($user, $licenseCounts) {
                        if ($license->users->contains('id', $user->id)) {
                            if (isset($licenseCounts[$license->id])) {
                                $license->active_activations_count = $licenseCounts[$license->id]->active_activations_count;
                            }
                            return true;
                        }
                        return false;
                    }));
                });
            });
        }

        return view('user.licenses.index', [
            'rootProducts' => $rootProducts,
        ]);
    }

    public function show(Request $request, License $license): View
    {
        // Use accessibleLicenses to include shared licenses
        $license = $request->user()
            ->accessibleLicenses()
            ->whereKey($license->id)
            ->with([
                'product.parent',
                'subProduct.parent',
                'licenseType',
                'activations' => fn ($query) => $query->latest(),
            ])
            ->withCount([
                'activations',
                'activations as active_activations_count' => fn ($query) => $query->where('status', LicenseActivation::STATUS_ACTIVE),
            ])
            ->firstOrFail();

        return view('user.licenses.show', [
            'license' => $license,
        ]);
    }

    public function showKey(Request $request, License $license): JsonResponse
    {
        // Use accessibleLicenses to include shared licenses
        $license = $request->user()
            ->accessibleLicenses()
            ->whereKey($license->id)
            ->firstOrFail();

        $licenseKey = $license->revealLicenseKey();

        if (! $licenseKey) {
            return response()->json([
                'message' => 'License key cannot be decrypted. Contact support.',
            ], 422);
        }

        return response()->json([
            'license_key' => $licenseKey,
            'masked_license_key' => $license->masked_license_key,
        ]);
    }
}
