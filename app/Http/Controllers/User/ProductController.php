<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        return view('user.products.index', [
            'entitlements' => $request->user()
                ->entitlements()
                ->current()
                ->whereHas('product', function ($query) {
                    $query->whereNull('parent_id');
                })
                ->with('product.parent')
                ->latest()
                ->paginate(12),
        ]);
    }

    public function show(Request $request, Product $product): View
    {
        $product->loadMissing(['parent.parent', 'subProducts']);

        $entitlement = $request->user()
            ->entitlements()
            ->current()
            ->whereIn('product_id', $product->getAncestorIdsAndSelf())
            ->with('product.parent')
            ->firstOrFail();

        $licenses = $request->user()
            ->licenses()
            ->where(function ($query) use ($product): void {
                $query->where('product_id', $product->id)
                    ->orWhere('sub_product_id', $product->id);
            })
            ->with(['licenseType', 'activations'])
            ->latest()
            ->get();

        $downloads = $product->downloadItems()
            ->availableForUser($request->user())
            ->latest()
            ->get();

        // Calculate entitlement summary metrics
        $activeLicenseCount = $licenses->filter(function ($license) {
            return $license->expired_date === null || $license->expired_date->isFuture();
        })->count();

        $totalDownloadsCount = $request->user()
            ->downloadLogs()
            ->whereHas('downloadItem', function ($query) use ($product) {
                $query->where('product_id', $product->id);
            })
            ->count();

        $daysRemaining = null;
        if ($entitlement->end_date) {
            $daysRemaining = max(0, now()->diffInDays($entitlement->end_date, false));
        }

        $lastAccessedDate = $request->user()
            ->downloadLogs()
            ->whereHas('downloadItem', function ($query) use ($product) {
                $query->where('product_id', $product->id);
            })
            ->latest('downloaded_at')
            ->first()
            ?->downloaded_at;

        return view('user.products.show', [
            'entitlement' => $entitlement,
            'product' => $product,
            'breadcrumbs' => $product->getBreadcrumbs(),
            'licenses' => $licenses,
            'downloads' => $downloads,
            'activeLicenseCount' => $activeLicenseCount,
            'totalDownloadsCount' => $totalDownloadsCount,
            'daysRemaining' => $daysRemaining,
            'lastAccessedDate' => $lastAccessedDate,
        ]);
    }
}
