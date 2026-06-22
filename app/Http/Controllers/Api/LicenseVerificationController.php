<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LicenseVerificationController extends Controller
{
    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'license_key' => ['required', 'string'],
            'device_fingerprint' => ['nullable', 'string'],
        ]);

        return response()->json([
            'valid' => false,
            'error' => 'License not found',
        ], 404);
    }

    public function activate(Request $request): JsonResponse
    {
        $request->validate([
            'license_key' => ['required', 'string'],
            'device_fingerprint' => ['nullable', 'string'],
            'hostname' => ['nullable', 'string'],
        ]);

        return $this->notImplemented();
    }

    public function userProducts(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        return $this->notImplemented();
    }

    public function productLicenses(Request $request): JsonResponse
    {
        $request->validate([
            'product_code' => ['required', 'string'],
        ]);

        return $this->notImplemented();
    }

    private function notImplemented(): JsonResponse
    {
        return response()->json([
            'error' => 'This API endpoint is reserved for a later implementation phase.',
        ], 501);
    }
}
