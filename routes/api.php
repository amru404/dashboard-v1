<?php

use App\Http\Controllers\Api\LicenseVerificationController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:30,1')->group(function (): void {
    Route::post('/license/verify', [LicenseVerificationController::class, 'verify']);
    Route::post('/license/activate', [LicenseVerificationController::class, 'activate']);
    Route::post('/user/products', [LicenseVerificationController::class, 'userProducts']);
    Route::post('/product/licenses', [LicenseVerificationController::class, 'productLicenses']);
});
