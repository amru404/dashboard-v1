<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LicenseActivation;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LicenseActivationController extends Controller
{
    public function index(): View
    {
        return view('admin.license-activations.index', [
            'activations' => LicenseActivation::query()
                ->with(['license.product', 'license.user.organization'])
                ->latest()
                ->paginate(20),
        ]);
    }

    public function destroy(LicenseActivation $licenseActivation): RedirectResponse
    {
        $licenseActivation->delete();

        return redirect()
            ->route('admin.license-activations.index')
            ->with('status', 'Activation reset.');
    }
}
