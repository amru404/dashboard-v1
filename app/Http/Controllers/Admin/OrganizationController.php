<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrganizationController extends Controller
{
    public function index(): View
    {
        return view('admin.organizations.index', [
            'organizations' => Organization::query()
                ->withCount('users')
                ->latest()
                ->paginate(12),
        ]);
    }

    public function create(): View
    {
        return view('admin.organizations.create', [
            'organization' => new Organization(['is_active' => true]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        $organization = Organization::query()->create($data);

        return redirect()
            ->route('admin.organizations.show', $organization)
            ->with('status', 'Organization created.');
    }

    public function show(Organization $organization): View
    {
        $organization->load([
            'users' => fn ($query) => $query->latest()->limit(10),
        ])->loadCount('users');

        return view('admin.organizations.show', [
            'organization' => $organization,
        ]);
    }

    public function edit(Organization $organization): View
    {
        return view('admin.organizations.edit', [
            'organization' => $organization,
        ]);
    }

    public function update(Request $request, Organization $organization): RedirectResponse
    {
        $organization->update($this->validatedData($request, $organization));

        return redirect()
            ->route('admin.organizations.show', $organization)
            ->with('status', 'Organization updated.');
    }

    public function destroy(Organization $organization): RedirectResponse
    {
        if ($organization->users()->exists()) {
            return redirect()
                ->route('admin.organizations.show', $organization)
                ->withErrors(['organization' => 'Organizations with users cannot be deleted. Mark the organization inactive instead.']);
        }

        $organization->delete();

        return redirect()
            ->route('admin.organizations.index')
            ->with('status', 'Organization deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedData(Request $request, ?Organization $organization = null): array
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
                Rule::unique('organizations', 'code')->ignore($organization),
            ],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
