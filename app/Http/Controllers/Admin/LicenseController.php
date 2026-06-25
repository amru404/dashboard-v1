<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Models\LicenseActivation;
use App\Models\LicenseType;
use App\Models\Product;
use App\Models\User;
use App\Support\ProductTreeBuilder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LicenseController extends Controller
{
    public function __construct(private readonly ProductTreeBuilder $productTreeBuilder)
    {
    }

    public function index(): View
    {
        return view('admin.licenses.index', [
            'licenses' => License::query()
                ->with(['product', 'subProduct', 'user.organization', 'licenseType'])
                ->withCount('activeActivations')
                ->latest()
                ->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.licenses.create', [
            'license' => new License([
                'quantity' => 1,
            ]),
            ...$this->formData(),
        ]);
    }

    public function batchCreate(): View
    {
        return view('admin.licenses.batch-create', [
            'license' => new License([
                'quantity' => 1,
            ]),
            ...$this->formData(),
        ]);
    }

    public function generateKey(Request $request): JsonResponse|RedirectResponse
    {
        $quantity = (int) $request->input('quantity', 1);
        $quantity = max(1, min(100, $quantity));

        $keys = collect(range(1, $quantity))
            ->map(fn () => $this->uniqueGeneratedKey())
            ->values()
            ->all();

        if ($request->expectsJson()) {
            if ($quantity === 1) {
                return response()->json(['license_key' => $keys[0]]);
            }

            return response()->json(['license_keys' => $keys]);
        }

        // Fallback for non-AJAX usage (single key)
        return back()
            ->withInput()
            ->with('generated_license_key', $keys[0]);
    }

    public function store(Request $request): RedirectResponse
    {
        if ($request->has('license_keys')) {
            $request->merge([
                'license_count' => count($request->input('license_keys', [])),
            ]);

            $data = $this->validatedBatchData($request);
            $this->validateSubProduct($data);

            $data['client_name'] = $this->clientNameForUser((int) $data['user_id']);
            $licenseCount = (int) $data['license_count'];
            $providedKeys = $data['license_keys'] ?? null;

            unset($data['license_count'], $data['license_keys']);

            $createdLicenses = DB::transaction(function () use ($data, $licenseCount, $providedKeys): Collection {
                return collect(range(1, $licenseCount))
                    ->map(function ($i) use ($data, $providedKeys): License {
                        $key = null;

                        if (is_array($providedKeys) && array_key_exists($i - 1, $providedKeys) && trim((string) $providedKeys[$i - 1]) !== '') {
                            $key = (string) $providedKeys[$i - 1];
                        }

                        if (! $key) {
                            $key = $this->uniqueGeneratedKey();
                        }

                        return License::query()->create(array_merge($data, [
                            'license_key' => $key,
                        ]));
                    });
            });

            return redirect()
                ->route('admin.licenses.index')
                ->with('status', $createdLicenses->count().' licenses created.');
        }

        if (blank($request->input('license_key')) && $request->has('license_keys.0')) {
            $request->merge(['license_key' => (string) $request->input('license_keys.0')]);
        }

        $data = $this->validatedData($request, licenseKeyRequired: true);
        $this->validateSubProduct($data);

        $data['client_name'] = $this->clientNameForUser((int) $data['user_id']);

        $license = License::query()->create($data);

        return redirect()
            ->route('admin.licenses.show', $license)
            ->with('status', 'License created.');
    }

    public function batchStore(Request $request): RedirectResponse
    {
        $data = $this->validatedBatchData($request);
        $this->validateSubProduct($data);

        $data['client_name'] = $this->clientNameForUser((int) $data['user_id']);
        $licenseCount = (int) $data['license_count'];
        $providedKeys = $data['license_keys'] ?? null;
        // normalize provided keys
        if (is_array($providedKeys)) {
            $providedKeys = array_values($providedKeys);
        }

        unset($data['license_count'], $data['license_keys']);

        $createdLicenses = DB::transaction(function () use ($data, $licenseCount, $providedKeys): Collection {
            return collect(range(1, $licenseCount))
                ->map(function ($i) use ($data, $providedKeys): License {
                    $key = null;

                    if (is_array($providedKeys) && array_key_exists($i - 1, $providedKeys) && trim((string) $providedKeys[$i - 1]) !== '') {
                        $key = (string) $providedKeys[$i - 1];
                    }

                    if (! $key) {
                        $key = $this->uniqueGeneratedKey();
                    }

                    return License::query()->create(array_merge($data, [
                        'license_key' => $key,
                    ]));
                });
        });

        if ($request->expectsJson()) {
            return response()->json([
                'created' => $createdLicenses->count(),
                'redirect' => route('admin.licenses.index'),
            ]);
        }

        return redirect()
            ->route('admin.licenses.index')
            ->with('status', $createdLicenses->count().' licenses created.');
    }

    public function show(License $license): View
    {
        $license->load([
            'product',
            'subProduct',
            'user.organization',
            'licenseType',
            'activations' => fn ($query) => $query->latest(),
        ]);

        return view('admin.licenses.show', [
            'license' => $license,
        ]);
    }

    public function edit(License $license): View
    {
        $license->load(['user.organization', 'product', 'subProduct', 'licenseType']);

        return view('admin.licenses.edit', [
            'license' => $license,
            ...$this->formData(),
        ]);
    }

    public function update(Request $request, License $license): RedirectResponse
    {
        $data = $this->validatedData($request, $license);
        $this->validateSubProduct($data);

        if (blank($data['license_key'] ?? null)) {
            unset($data['license_key']);
        }

        $data['client_name'] = $this->clientNameForUser((int) $data['user_id']);

        $license->update($data);

        return redirect()
            ->route('admin.licenses.show', $license)
            ->with('status', 'License updated.');
    }

    public function destroy(License $license): RedirectResponse
    {
        $license->delete();

        return redirect()
            ->route('admin.licenses.index')
            ->with('status', 'License deleted.');
    }

    public function revealKey(License $license): JsonResponse
    {
        return $this->showKey($license);
    }

    public function showKey(License $license): JsonResponse
    {
        $licenseKey = $license->revealLicenseKey();

        if (! $licenseKey) {
            return response()->json([
                'message' => 'License key cannot be decrypted. Check APP_KEY history.',
            ], 422);
        }

        return response()->json([
            'license_key' => $licenseKey,
            'masked_license_key' => $license->masked_license_key,
        ]);
    }

    public function resetActivation(Request $request, License $license): RedirectResponse
    {
        $data = $request->validate([
            'strategy' => ['required', Rule::in(['delete', 'deactivate'])],
        ]);

        $count = $license->activations()->count();

        if ($data['strategy'] === 'deactivate') {
            $license->activations()->update(['status' => LicenseActivation::STATUS_INACTIVE]);

            return redirect()
                ->route('admin.licenses.show', $license)
                ->with('status', $count.' activations marked inactive.');
        }

        $license->activations()->delete();

        return redirect()
            ->route('admin.licenses.show', $license)
            ->with('status', $count.' activations removed.');
    }

    public function destroyActivation(LicenseActivation $activation): RedirectResponse
    {
        $license = $activation->license;
        $activation->delete();

        return redirect()
            ->route('admin.licenses.show', $license)
            ->with('status', 'Activation removed.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedData(Request $request, ?License $license = null, bool $licenseKeyRequired = false): array
    {
        return $request->validate([
            'user_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where(fn ($query) => $query->where('role', User::ROLE_USER)),
            ],
            'product_id' => ['required', 'integer', Rule::exists('products', 'id')],
            'sub_product_id' => ['nullable', 'integer', Rule::exists('products', 'id')],
            'license_type_id' => ['required', 'integer', Rule::exists('license_types', 'id')],
            'quantity' => ['required', 'integer', 'min:1', 'max:999999'],
            'max_activations' => ['nullable', 'integer', 'min:1', 'max:999999'],
            'expired_date' => ['nullable', 'date'],
            'license_key' => [
                $licenseKeyRequired ? 'required' : 'nullable',
                'string',
                'max:255',
                function (string $attribute, mixed $value, \Closure $fail) use ($license): void {
                    if (blank($value)) {
                        return;
                    }

                    $exists = License::query()
                        ->where('license_key_hash', License::licenseKeyHash((string) $value))
                        ->when($license, fn ($query) => $query->whereKeyNot($license->getKey()))
                        ->exists();

                    if ($exists) {
                        $fail('This license key has already been issued.');
                    }
                },
            ],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedBatchData(Request $request): array
    {
        return $request->validate([
            'user_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where(fn ($query) => $query->where('role', User::ROLE_USER)),
            ],
            'product_id' => ['required', 'integer', Rule::exists('products', 'id')],
            'sub_product_id' => ['nullable', 'integer', Rule::exists('products', 'id')],
            'license_type_id' => ['required', 'integer', Rule::exists('license_types', 'id')],
            'quantity' => ['required', 'integer', 'min:1', 'max:999999'],
            'max_activations' => ['nullable', 'integer', 'min:1', 'max:999999'],
            'expired_date' => ['nullable', 'date'],
            'license_count' => ['required', 'integer', 'min:1', 'max:100'],
            'license_keys' => ['nullable', 'array'],
            'license_keys.*' => ['string', 'max:255'],
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function validateSubProduct(array $data): void
    {
        if (blank($data['sub_product_id'] ?? null)) {
            return;
        }

        $product = Product::query()->findOrFail((int) $data['product_id']);
        $subProductId = (int) $data['sub_product_id'];

        if ($subProductId === (int) $product->id || ! in_array($subProductId, $product->getAllDescendantIds(), true)) {
            throw ValidationException::withMessages([
                'sub_product_id' => 'The sub-product must be a child or descendant of the selected product.',
            ]);
        }
    }

    /**
     * @return array{users: EloquentCollection<int, User>, licenseTypes: EloquentCollection<int, LicenseType>, productOptions: Collection<int, array{id: int, label: string, path: string, depth: int}>}
     */
    private function formData(): array
    {
        return [
            'users' => User::query()
                ->with('organization')
                ->where('role', User::ROLE_USER)
                ->orderBy('name')
                ->get(),
            'licenseTypes' => LicenseType::query()
                ->orderBy('name')
                ->get(),
            'productOptions' => $this->productTreeBuilder->options(),
        ];
    }

    private function clientNameForUser(int $userId): string
    {
        $user = User::query()->with('organization')->findOrFail($userId);

        return $user->organization?->name ?? $user->name;
    }

    private function uniqueGeneratedKey(): string
    {
        do {
            $licenseKey = License::generateKey();
        } while (License::query()->where('license_key_hash', License::licenseKeyHash($licenseKey))->exists());

        return $licenseKey;
    }
}
