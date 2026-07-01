<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Entitlement;
use App\Models\User;
use App\Support\ProductTreeBuilder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class EntitlementController extends Controller
{
    public function __construct(private readonly ProductTreeBuilder $productTreeBuilder)
    {
    }

    public function index(Request $request): View
    {
        $query = Entitlement::query()
            ->with(['user.organization', 'product']);

        // Search filter
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhereHas('user.organization', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('product', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                });
            });
        }

        // Status filter
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        return view('admin.entitlements.index', [
            'entitlements' => $query->latest()->paginate(20)->withQueryString(),
        ]);
    }

    public function create(): View
    {
        return view('admin.entitlements.create', [
            'entitlement' => new Entitlement([
                'start_date' => now(),
                'status' => Entitlement::STATUS_ACTIVE,
            ]),
            ...$this->formData(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $entitlement = Entitlement::query()->create($this->validatedData($request));

        return redirect()
            ->route('admin.entitlements.show', $entitlement)
            ->with('status', 'Entitlement granted.');
    }

    public function show(Entitlement $entitlement): View
    {
        $entitlement->load(['user.organization', 'product.parent']);

        return view('admin.entitlements.show', [
            'entitlement' => $entitlement,
        ]);
    }

    public function edit(Entitlement $entitlement): View
    {
        return view('admin.entitlements.edit', [
            'entitlement' => $entitlement,
            ...$this->formData(),
        ]);
    }

    public function update(Request $request, Entitlement $entitlement): RedirectResponse
    {
        $entitlement->update($this->validatedData($request, $entitlement));

        return redirect()
            ->route('admin.entitlements.show', $entitlement)
            ->with('status', 'Entitlement updated.');
    }

    public function destroy(Entitlement $entitlement): RedirectResponse
    {
        $entitlement->delete();

        return redirect()
            ->route('admin.entitlements.index')
            ->with('status', 'Entitlement deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedData(Request $request, ?Entitlement $entitlement = null): array
    {
        $data = $request->validate([
            'user_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where(fn ($query) => $query->whereIn('role', [User::ROLE_CLIENT, User::ROLE_PARTNER])),
            ],
            'product_id' => [
                'required',
                'integer',
                Rule::exists('products', 'id')->where(fn ($query) => $query->whereNull('parent_id')),
            ],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'download_expired_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['required', Rule::in(Entitlement::statuses())],
        ]);

        $exists = Entitlement::query()
            ->where('user_id', $data['user_id'])
            ->where('product_id', $data['product_id'])
            ->when($entitlement, fn ($query) => $query->whereKeyNot($entitlement->getKey()))
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'product_id' => 'This user already has an entitlement for the selected product.',
            ]);
        }

        return $data;
    }

    /**
     * @return array{users: EloquentCollection<int, User>, productOptions: Collection<int, array{id: int, label: string, path: string, depth: int}>, statuses: array<int, string>}
     */
    private function formData(): array
    {
        return [
            'users' => User::query()
                ->with('organization')
                ->whereIn('role', [User::ROLE_CLIENT, User::ROLE_PARTNER])
                ->orderBy('name')
                ->get(),
            'productOptions' => $this->productTreeBuilder->options(rootOnly: true),
            'statuses' => Entitlement::statuses(),
        ];
    }
}
