<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class SettingsController extends Controller
{
    public function index()
    {
        $licenseKeyLength = Setting::get('license_key_length', 32);
        
        return view('admin.settings.index', [
            'licenseKeyLength' => $licenseKeyLength,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'license_key_length' => 'required|integer|min:8|max:256',
        ]);

        Setting::set('license_key_length', $validated['license_key_length']);

        return redirect()->route('admin.settings.index')
            ->with('success', 'License key length updated successfully.');
    }
}
