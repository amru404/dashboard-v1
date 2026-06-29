<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DownloadController;
use App\Http\Controllers\Admin\EntitlementController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\LicenseActivationController;
use App\Http\Controllers\Admin\LicenseController;
use App\Http\Controllers\Admin\LicenseTypeController;
use App\Http\Controllers\Admin\OrganizationController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\QuotationController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\DownloadController as UserDownloadController;
use App\Http\Controllers\User\LicenseController as UserLicenseController;
use App\Http\Controllers\User\ProductController as UserProductController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user?->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }

    if ($user?->isUser()) {
        return redirect()->route('user.dashboard');
    }

    abort(403);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified', 'role:'.User::ROLE_ADMIN])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
        Route::put('settings', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');
        Route::resource('organizations', OrganizationController::class);
        Route::resource('users', UserController::class);
        Route::resource('products', ProductController::class);
        Route::resource('license-types', LicenseTypeController::class);
        Route::post('licenses/generate-key', [LicenseController::class, 'generateKey'])->name('licenses.generate-key');
        Route::get('licenses/batch-create', [LicenseController::class, 'batchCreate'])->name('licenses.batch-create');
        Route::post('licenses/batch-store', [LicenseController::class, 'batchStore'])->name('licenses.batch-store');
        Route::get('licenses/generate-bulk', [LicenseController::class, 'generateBulk'])->name('licenses.generate-bulk');
        Route::post('licenses/bulk-store', [LicenseController::class, 'bulkStore'])->name('licenses.bulk-store');
        Route::get('licenses/{license}/add-keys', [LicenseController::class, 'addKeys'])->name('licenses.add-keys');
        Route::post('licenses/{license}/store-keys', [LicenseController::class, 'storeKeys'])->name('licenses.store-keys');
        Route::get('licenses/{license}/show-key', [LicenseController::class, 'showKey'])->name('licenses.show-key');
        Route::post('licenses/{license}/reveal-key', [LicenseController::class, 'revealKey'])->name('licenses.reveal-key');
        Route::post('licenses/{license}/reset-activation', [LicenseController::class, 'resetActivation'])->name('licenses.reset-activation');
        Route::delete('licenses/activation/{activation}', [LicenseController::class, 'destroyActivation'])->name('licenses.activation.destroy');
        Route::resource('licenses', LicenseController::class);
        Route::resource('license-activations', LicenseActivationController::class)->only(['index', 'destroy']);
        Route::resource('entitlements', EntitlementController::class);
        Route::resource('download-items', DownloadController::class);
        Route::get('invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download');
        Route::resource('invoices', InvoiceController::class);
        Route::get('quotations/{quotation}/download', [QuotationController::class, 'download'])->name('quotations.download');
        Route::resource('quotations', QuotationController::class);
    });

Route::middleware(['auth', 'verified', 'role:'.User::ROLE_USER])
    ->prefix('user')
    ->name('user.')
    ->group(function (): void {
        Route::get('/', [UserDashboardController::class, 'index'])->name('dashboard');
        Route::resource('products', UserProductController::class)->only(['index', 'show']);
        Route::get('licenses/{license}/show-key', [UserLicenseController::class, 'showKey'])->name('licenses.show-key');
        Route::resource('licenses', UserLicenseController::class)->only(['index', 'show']);
        Route::get('downloads/{downloadItem}/download', [UserDownloadController::class, 'download'])->name('downloads.download');
        Route::resource('downloads', UserDownloadController::class)->only(['index']);
        Route::get('invoices/{invoice}/download', [\App\Http\Controllers\User\InvoiceController::class, 'download'])->name('invoices.download');
        Route::resource('invoices', \App\Http\Controllers\User\InvoiceController::class)->only(['index']);
        Route::get('quotations/{quotation}/download', [\App\Http\Controllers\User\QuotationController::class, 'download'])->name('quotations.download');
        Route::resource('quotations', \App\Http\Controllers\User\QuotationController::class)->only(['index']);
    });

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
