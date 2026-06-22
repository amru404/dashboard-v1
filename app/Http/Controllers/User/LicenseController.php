<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Models\LicenseActivation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LicenseController extends Controller
{
    public function index(Request $request): View
    {
        return view('user.licenses.index', [
            'licenses' => $request->user()
                ->licenses()
                ->with(['product', 'subProduct', 'licenseType'])
                ->withCount([
                    'activations',
                    'activations as active_activations_count' => fn ($query) => $query->where('status', LicenseActivation::STATUS_ACTIVE),
                ])
                ->latest()
                ->paginate(12),
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
