<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LicenseType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LicenseTypeController extends Controller
{
    public function index(): View
    {
        return view('admin.license-types.index', [
            'licenseTypes' => LicenseType::query()
                ->withCount('licenses')
                ->orderBy('name')
                ->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.license-types.create', [
            'licenseType' => new LicenseType(['is_active' => true, 'include_in_packages' => true]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $licenseType = LicenseType::query()->create($this->validatedData($request));

        return redirect()
            ->route('admin.license-types.show', $licenseType)
            ->with('status', 'License type created.');
    }

    public function show(LicenseType $licenseType): View
    {
        $licenseType->load([
            'licenses' => fn ($query) => $query
                ->with(['user.organization', 'product'])
                ->latest()
                ->limit(10),
        ])->loadCount('licenses');

        return view('admin.license-types.show', [
            'licenseType' => $licenseType,
        ]);
    }

    public function edit(LicenseType $licenseType): View
    {
        return view('admin.license-types.edit', [
            'licenseType' => $licenseType,
        ]);
    }

    public function update(Request $request, LicenseType $licenseType): RedirectResponse
    {
        $licenseType->update($this->validatedData($request, $licenseType));

        return redirect()
            ->route('admin.license-types.show', $licenseType)
            ->with('status', 'License type updated.');
    }

    public function destroy(LicenseType $licenseType): RedirectResponse
    {
        if ($licenseType->licenses()->exists()) {
            return redirect()
                ->route('admin.license-types.show', $licenseType)
                ->withErrors(['license_type' => 'License types assigned to licenses cannot be deleted. Mark the type inactive instead.']);
        }

        $licenseType->delete();

        return redirect()
            ->route('admin.license-types.index')
            ->with('status', 'License type deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedData(Request $request, ?LicenseType $licenseType = null): array
    {
        $request->merge([
            'code' => Str::upper(Str::slug((string) $request->input('code'))),
        ]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('license_types', 'code')->ignore($licenseType),
            ],
            'is_active' => ['sometimes', 'boolean'],
            'include_in_packages' => ['sometimes', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['include_in_packages'] = $request->boolean('include_in_packages');

        return $data;
    }
}
