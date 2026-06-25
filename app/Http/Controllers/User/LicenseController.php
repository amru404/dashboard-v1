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

        $licensedProductIds = $user->licenses()
            ->pluck('product_id')
            ->merge($user->licenses()->whereNotNull('sub_product_id')->pluck('sub_product_id'))
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
                'licenses' => fn ($query) => $query->where('user_id', $user->id)->with('licenseType'),
                'allChildren.licenses' => fn ($query) => $query->where('user_id', $user->id)->with('licenseType'),
            ])
            ->orderBy('name')
            ->paginate(12);

        return view('user.licenses.index', [
            'rootProducts' => $rootProducts,
        ]);
    }

    public function show(Request $request, License $license): View
    {
        $license = $request->user()
            ->licenses()
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
        $license = $request->user()
            ->licenses()
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
