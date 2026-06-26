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
        $product->loadMissing(['parent.parent', 'subProducts', 'subProducts.allChildren']);

        $entitlement = $request->user()
            ->entitlements()
            ->current()
            ->where('product_id', $product->id)
            ->with('product.parent')
            ->firstOrFail();

        $productIds = array_merge([$product->id], $product->getAllDescendantIds());

        $licenses = $request->user()
            ->licenses()
            ->where(function ($query) use ($productIds): void {
                $query->whereIn('product_id', $productIds)
                    ->orWhereIn('sub_product_id', $productIds);
            })
            ->with(['licenseType', 'activations', 'product', 'subProduct'])
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
