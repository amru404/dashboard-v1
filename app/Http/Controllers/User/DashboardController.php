<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\DownloadItem;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        return view('user.dashboard', [
            'activeEntitlementCount' => $user->entitlements()->current()->count(),
            'licenseCount' => $user->licenses()->count(),
            'activeLicenseCount' => $user->licenses()->active()->count(),
            'expiringLicenseCount' => $user->licenses()->expiringSoon()->count(),
            'expiredLicenseCount' => $user->licenses()->expired()->count(),
            'downloadCount' => DownloadItem::query()
                ->availableForUser($user)
                ->count(),
            'availableDownloads' => DownloadItem::query()
                ->availableForUser($user)
                ->with('product')
                ->latest()
                ->limit(5)
                ->get(),
            'ownedProducts' => $user
                ->entitlements()
                ->current()
                ->with('product.parent')
                ->get()
                ->pluck('product')
                ->filter()
                ->map(fn ($product) => $product->parent ?: $product)
                ->unique('id')
                ->values()
                ->take(5),
            'recentLicenses' => $user
                ->licenses()
                ->with(['product', 'subProduct', 'licenseType'])
                ->latest()
                ->limit(5)
                ->get(),
            'recentDownloadLogs' => $user
                ->downloadLogs()
                ->with('downloadItem.product')
                ->latest('downloaded_at')
                ->limit(5)
                ->get(),
        ]);
    }
}
