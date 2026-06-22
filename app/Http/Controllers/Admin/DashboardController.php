<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Models\Organization;
use App\Models\Product;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'organizationCount' => Organization::query()->count(),
            'activeOrganizationCount' => Organization::query()->active()->count(),
            'userCount' => User::query()->count(),
            'activeUserCount' => User::query()->where('is_active', true)->count(),
            'productCount' => Product::query()->count(),
            'activeProductCount' => Product::query()->active()->count(),
            'licenseCount' => License::query()->count(),
            'expiringLicenseCount' => License::query()->expiringSoon()->count(),
        ]);
    }
}
