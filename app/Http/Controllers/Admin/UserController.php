<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'organization_id' => ['nullable', 'integer', 'exists:organizations,id'],
            'role' => ['nullable', Rule::in($this->roles())],
            'status' => ['nullable', Rule::in(['active', 'inactive'])],
            'search' => ['nullable', 'string', 'max:255'],
        ]);

        return view('admin.users.index', [
            'users' => User::query()
                ->with('organization')
                ->when($filters['organization_id'] ?? null, fn ($query, $organizationId) => $query->where('organization_id', $organizationId))
                ->when($filters['role'] ?? null, fn ($query, $role) => $query->where('role', $role))
                ->when(($filters['status'] ?? null) === 'active', fn ($query) => $query->where('is_active', true))
                ->when(($filters['status'] ?? null) === 'inactive', fn ($query) => $query->where('is_active', false))
                ->when($filters['search'] ?? null, function ($query, $search): void {
                    $query->where(function ($query) use ($search): void {
                        $query->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
                })
                ->latest()
                ->paginate(12)
                ->withQueryString(),
            'filters' => $filters,
            'organizations' => $this->organizations(),
            'roles' => $this->roles(),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create', [
            'organizations' => $this->organizations(),
            'user' => new User(['role' => User::ROLE_USER, 'is_active' => true]),
            'roles' => $this->roles(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request, passwordRequired: true);

        $user = User::query()->create($data);

        return redirect()
            ->route('admin.users.show', $user)
            ->with('status', 'User created.');
    }

    public function show(User $user): View
    {
        $user->load('organization');

        return view('admin.users.show', [
            'user' => $user,
        ]);
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'organizations' => $this->organizations(),
            'roles' => $this->roles(),
            'user' => $user,
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $this->validatedData($request, $user);

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        if ($user->is(Auth::user())) {
            $data['is_active'] = true;
        }

        $user->update($data);

        return redirect()
            ->route('admin.users.show', $user)
            ->with('status', 'User updated.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->is(Auth::user())) {
            return redirect()
                ->route('admin.users.index')
                ->withErrors(['user' => 'You cannot delete your own account.']);
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'User deleted.');
    }

    /**
     * @return array<int, string>
     */
    private function roles(): array
    {
        return [
            User::ROLE_ADMIN,
            User::ROLE_USER,
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Organization>
     */
    private function organizations()
    {
        return Organization::query()
            ->orderBy('name')
            ->get();
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedData(Request $request, ?User $user = null, bool $passwordRequired = false): array
    {
        $rules = [
            'organization_id' => ['required_if:role,'.User::ROLE_USER, 'nullable', 'exists:organizations,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user),
            ],
            'password' => [$passwordRequired ? 'required' : 'nullable', 'string', 'min:8'],
            'role' => ['required', Rule::in($this->roles())],
            'is_active' => ['sometimes', 'boolean'],
        ];

        $data = $request->validate($rules);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
