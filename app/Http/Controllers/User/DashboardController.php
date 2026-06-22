<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\DownloadItem;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        return view('user.dashboard', [
            'activeEntitlementCount' => $user->entitlements()->current()->count(),
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
                ->limit(6)
                ->get(),
            'ownedProducts' => $user
                ->entitlements()
                ->current()
                ->with('product')
                ->latest()
                ->limit(6)
                ->get()
                ->pluck('product')
                ->filter(),
            'recentLicenses' => $user
                ->licenses()
                ->with(['product', 'licenseType'])
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
