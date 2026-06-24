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
                ->with('product.parent')
                ->latest()
                ->paginate(12),
        ]);
    }

    public function show(Request $request, Product $product): View
    {
        $product->loadMissing('parent.parent');

        $entitlement = $request->user()
            ->entitlements()
            ->current()
            ->where('product_id', $product->id)
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

        return view('user.products.show', [
            'entitlement' => $entitlement,
            'product' => $product,
            'breadcrumbs' => $product->getBreadcrumbs(),
            'licenses' => $licenses,
            'downloads' => $downloads,
        ]);
    }
}
