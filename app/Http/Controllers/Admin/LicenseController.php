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
        $licenses = License::query()
            ->with(['product', 'subProduct', 'user.organization', 'licenseType'])
            ->withCount('activeActivations')
            ->latest()
            ->get();

        $products = Product::query()
            ->whereNull('parent_id')
            ->with(['subProducts' => fn ($query) => $query->orderBy('name')])
            ->active()
            ->get();

        return view('admin.licenses.index', [
            'licenses' => $licenses,
            'products' => $products,
        ]);
    }

    public function create(Request $request): View
    {
        $license = new License([
            'quantity' => 1,
            'product_id' => $request->query('product_id'),
            'sub_product_id' => $request->query('sub_product_id'),
        ]);

        return view('admin.licenses.create', [
            'license' => $license,
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

    public function generateBulk(Request $request): View
    {
        $products = Product::query()
            ->whereNull('parent_id')
            ->with('subProducts', 'allChildren')
            ->active()
            ->orderBy('name')
            ->get();

        return view('admin.licenses.generate-bulk', [
            'products' => $products,
            'users' => User::query()
                ->with('organization')
                ->where('role', User::ROLE_USER)
                ->orderBy('name')
                ->get(),
            'licenseTypes' => LicenseType::query()
                ->orderBy('name')
                ->get(),
            'licenseKeyLength' => \App\Models\Setting::get('license_key_length', 32),
        ]);
    }

    public function bulkStore(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'user_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where(fn ($query) => $query->where('role', User::ROLE_USER)),
            ],
            'product_id' => ['required', 'integer', Rule::exists('products', 'id')],
            'license_type_id' => ['required', 'integer', Rule::exists('license_types', 'id')],
            'max_activations' => ['nullable', 'integer', 'min:1', 'max:999999'],
            'expired_date' => ['nullable', 'date'],
            'licenses' => ['required', 'array', 'min:1'],
            'licenses.*.sub_product_id' => ['required', 'integer', Rule::exists('products', 'id')],
            'licenses.*.license_key' => ['required', 'string', 'max:255'],
        ]);

        $user = User::query()->findOrFail((int) $data['user_id']);
        $clientName = $user->organization?->name ?? $user->name;
        $parentProduct = Product::query()->findOrFail((int) $data['product_id']);

        // Validate all sub-products are descendants of the parent product
        $allowedSubProductIds = $parentProduct->getAllDescendantIds();
        foreach ($data['licenses'] as $licenseData) {
            if (!in_array((int) $licenseData['sub_product_id'], $allowedSubProductIds, true)) {
                throw ValidationException::withMessages([
                    'licenses' => 'One or more sub-products are not descendants of the selected parent product.',
                ]);
            }
        }

        // Validate all license keys are unique
        $providedKeys = array_map(fn($l) => License::normalizeLicenseKey($l['license_key']), $data['licenses']);
        $providedKeyHashes = array_map(fn($k) => License::licenseKeyHash($k), $providedKeys);
        
        $existingCount = License::query()
            ->whereIn('license_key_hash', $providedKeyHashes)
            ->count();
            
        if ($existingCount > 0) {
            throw ValidationException::withMessages([
                'licenses' => 'One or more license keys already exist in the system.',
            ]);
        }

        $createdLicenses = collect();

        DB::transaction(function () use ($data, $clientName, &$createdLicenses): void {
            foreach ($data['licenses'] as $licenseData) {
                $createdLicenses->push(License::query()->create([
                    'user_id' => (int) $data['user_id'],
                    'product_id' => (int) $data['product_id'],
                    'sub_product_id' => (int) $licenseData['sub_product_id'],
                    'license_type_id' => (int) $data['license_type_id'],
                    'license_key' => $licenseData['license_key'],
                    'client_name' => $clientName,
                    'quantity' => 1,
                    'max_activations' => $data['max_activations'] ?? null,
                    'expired_date' => $data['expired_date'] ?? null,
                ]));
            }
        });

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'created' => $createdLicenses->count(),
                'message' => 'Generated '.$createdLicenses->count().' licenses successfully.',
            ]);
        }

        return redirect()
            ->route('admin.licenses.index')
            ->with('status', 'Generated '.$createdLicenses->count().' licenses successfully.');
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

    public function getUserProducts(User $user): JsonResponse
    {
        // Get all unique products (parent products only) that this user has licenses for
        $products = License::query()
            ->where('user_id', $user->id)
            ->whereNotNull('license_key')
            ->with('product')
            ->get()
            ->pluck('product')
            ->unique('id')
            ->filter(fn ($product) => $product !== null)
            ->map(fn ($product) => [
                'id' => $product->id,
                'name' => $product->name,
                'code' => $product->code,
            ])
            ->sortBy('name')
            ->values();

        return response()->json($products);
    }

    public function store(Request $request): RedirectResponse
    {
        // Handle batch creation when license_keys array is provided or no_license_key is checked
        if (($request->has('license_keys') && is_array($request->input('license_keys'))) || ($request->boolean('no_license_key'))) {
            $licenseCount = $request->input('license_count', count($request->input('license_keys', [])));
            $request->merge([
                'license_count' => $licenseCount,
                'license_mode' => 'new_license', // Ensure license_mode is set for batch creation
            ]);

            $data = $this->validatedBatchData($request);
            $this->validateSubProduct($data);

            $data['client_name'] = $this->clientNameForUser((int) $data['user_id']);
            $licenseCount = (int) $data['license_count'];
            $providedKeys = $data['license_keys'] ?? null;
            $noLicenseKey = (bool) ($data['no_license_key'] ?? false);

            // Normalize provided keys
            if (is_array($providedKeys)) {
                $providedKeys = array_values($providedKeys);
            }

            unset($data['license_count'], $data['license_keys'], $data['no_license_key']);
            
            $createdLicenses = DB::transaction(function () use ($data, $licenseCount, $providedKeys, $noLicenseKey): Collection {
                return collect(range(1, $licenseCount))
                    ->map(function ($i) use ($data, $providedKeys, $noLicenseKey): License {
                        $key = null;

                        // If no_license_key is true, set key to null
                        if ($noLicenseKey) {
                            $key = null;
                        } else {
                            // Check if a key was provided
                            if (is_array($providedKeys) && array_key_exists($i - 1, $providedKeys) && trim((string) $providedKeys[$i - 1]) !== '') {
                                $key = (string) $providedKeys[$i - 1];
                            }

                            // If no key provided, generate one
                            if (! $key) {
                                $key = $this->uniqueGeneratedKey();
                            }
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

        $data = $this->validatedData($request, licenseKeyRequired: false);

        // Handle Share License Mode
        if ($data['license_mode'] === 'share_license') {
            $sourceUser = User::query()->with('organization')->findOrFail((int) $data['source_user_id']);
            $assignUser = User::query()->with('organization')->findOrFail((int) $data['assign_user_id']);
            
            // Find existing license from source user for this product
            // Only get licenses that match the product_id (considering both parent and sub-product scenarios)
            $sourceLicense = License::query()
                ->where('user_id', (int) $data['source_user_id'])
                ->where(function ($query) use ($data) {
                    $productId = (int) $data['share_product_id'];
                    $query->where('product_id', $productId)
                          ->orWhere('sub_product_id', $productId);
                })
                ->whereNotNull('license_key')
                ->first();

            if (! $sourceLicense) {
                throw ValidationException::withMessages([
                    'source_user_id' => 'No license found for this product from the selected source client.',
                ]);
            }

            // Determine the correct product_id and sub_product_id based on the selected product
            $selectedProduct = Product::query()->findOrFail((int) $data['share_product_id']);
            
            // If the selected product is a parent product, use it as product_id
            // If it's a sub-product, find its parent and use that as product_id
            if ($selectedProduct->parent_id === null) {
                $targetProductId = $selectedProduct->id;
                $targetSubProductId = null;
            } else {
                $targetProductId = $selectedProduct->parent_id;
                $targetSubProductId = $selectedProduct->id;
            }

            // Create a new license with the same key for the assign user
            $license = License::query()->create([
                'user_id' => (int) $data['assign_user_id'],
                'product_id' => $targetProductId,
                'sub_product_id' => $targetSubProductId,
                'license_type_id' => $sourceLicense->license_type_id,
                'license_key' => $sourceLicense->license_key,
                'client_name' => $assignUser->organization?->name ?? $assignUser->name,
                'quantity' => 1,
                'max_activations' => $sourceLicense->max_activations,
                'expired_date' => $sourceLicense->expired_date,
            ]);

            return redirect()
                ->route('admin.licenses.show', $license)
                ->with('status', 'License shared successfully.');
        }

        // Handle New License Mode
        if (blank($request->input('license_key')) && $request->has('license_keys.0')) {
            $request->merge(['license_key' => (string) $request->input('license_keys.0')]);
        }

        $this->validateSubProduct($data);

        $data['client_name'] = $this->clientNameForUser((int) $data['user_id']);

        // Handle no_license_key option
        if ($data['no_license_key'] ?? false) {
            $data['license_key'] = null;
        } else {
            if (! ($data['license_key'] ?? null)) {
                $data['license_key'] = $this->uniqueGeneratedKey();
            }
        }

        // Remove fields not in License fillable
        $data = array_filter($data, fn ($key) => !in_array($key, ['license_mode', 'no_license_key', 'source_user_id', 'share_product_id', 'assign_user_id'], true), ARRAY_FILTER_USE_KEY);

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
        $noLicenseKey = (bool) ($data['no_license_key'] ?? false);
        
        // normalize provided keys
        if (is_array($providedKeys)) {
            $providedKeys = array_values($providedKeys);
        }

        unset($data['license_count'], $data['license_keys'], $data['no_license_key']);

        $createdLicenses = DB::transaction(function () use ($data, $licenseCount, $providedKeys, $noLicenseKey): Collection {
            return collect(range(1, $licenseCount))
                ->map(function ($i) use ($data, $providedKeys, $noLicenseKey): License {
                    $key = null;

                    // If no_license_key is true, set key to null
                    if ($noLicenseKey) {
                        $key = null;
                    } else {
                        // Check if a key was provided
                        if (is_array($providedKeys) && array_key_exists($i - 1, $providedKeys) && trim((string) $providedKeys[$i - 1]) !== '') {
                            $key = (string) $providedKeys[$i - 1];
                        }

                        // If no key provided, generate one
                        if (! $key) {
                            $key = $this->uniqueGeneratedKey();
                        }
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

    public function addKeys(License $license): View
    {
        $license->load(['product', 'user', 'licenseType']);

        return view('admin.licenses.add-keys', [
            'license' => $license,
        ]);
    }

    public function storeKeys(Request $request, License $license): RedirectResponse
    {
        $data = $request->validate([
            'license_keys' => ['required', 'array'],
            'license_keys.*' => ['array'],
            'license_keys.*.*' => ['required', 'string', 'max:255'],
        ]);

        $createdLicenses = collect();

        DB::transaction(function () use ($data, $license, &$createdLicenses): void {
            foreach ($data['license_keys'] as $subProductId => $keys) {
                $subProduct = Product::query()->findOrFail((int) $subProductId);

                // Validate sub-product is a descendant of parent
                if (!in_array($subProductId, $license->product->getAllDescendantIds(), true)) {
                    throw ValidationException::withMessages([
                        'license_keys' => 'Invalid sub-product selected.',
                    ]);
                }

                foreach ($keys as $key) {
                    if (blank($key)) continue;

                    // Validate key uniqueness
                    if (License::query()->where('license_key_hash', License::licenseKeyHash($key))->exists()) {
                        throw ValidationException::withMessages([
                            'license_keys' => 'One or more license keys already exist.',
                        ]);
                    }

                    $createdLicenses->push(License::query()->create([
                        'user_id' => $license->user_id,
                        'product_id' => $license->product_id,
                        'sub_product_id' => $subProductId,
                        'license_type_id' => $license->license_type_id,
                        'license_key' => $key,
                        'client_name' => $license->client_name,
                        'quantity' => 1,
                        'max_activations' => $license->max_activations,
                        'expired_date' => $license->expired_date,
                    ]));
                }
            }
        });

        return redirect()
            ->route('admin.licenses.show', $license)
            ->with('status', $createdLicenses->count().' sub-product keys added.');
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

        // Handle no_license_key option for update
        if ($data['no_license_key'] ?? false) {
            $data['license_key'] = null;
        }

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
        // If no_license_key is checked, license key is not required
        if ($request->boolean('no_license_key')) {
            $licenseKeyRequired = false;
        }
        
        $baseRules = [
            'license_mode' => ['required', Rule::in(['new_license', 'share_license'])],
            'user_id' => [
                'required_if:license_mode,new_license',
                'integer',
                Rule::exists('users', 'id')->where(fn ($query) => $query->where('role', User::ROLE_USER)),
            ],
            'product_id' => ['required_if:license_mode,new_license', 'integer', Rule::exists('products', 'id')],
            'sub_product_id' => ['nullable', 'integer', Rule::exists('products', 'id')],
            'no_license_key' => ['nullable', 'boolean'],
            'license_type_id' => ['required_if:license_mode,new_license', 'integer', Rule::exists('license_types', 'id')],
            'quantity' => ['required_if:license_mode,new_license', 'integer', 'min:1', 'max:999999'],
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
            'license_keys' => ['nullable', 'array'],
            'license_keys.*' => ['nullable', 'string', 'max:255'],
            // Share License Fields
            'source_user_id' => ['required_if:license_mode,share_license', 'integer', Rule::exists('users', 'id')],
            'share_product_id' => ['required_if:license_mode,share_license', 'integer', Rule::exists('products', 'id')],
            'assign_user_id' => ['required_if:license_mode,share_license', 'integer', Rule::exists('users', 'id')],
        ];

        $validated = $request->validate($baseRules);

        // Additional validation for share_license mode
        if ($request->input('license_mode') === 'share_license') {
            $sourceUserId = (int) $validated['source_user_id'];
            $assignUserId = (int) $validated['assign_user_id'];

            if ($sourceUserId === $assignUserId) {
                throw ValidationException::withMessages([
                    'assign_user_id' => 'The assign user must be different from the source user.',
                ]);
            }
        }

        return $validated;
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedBatchData(Request $request): array
    {
        return $request->validate([
            'license_mode' => ['nullable', Rule::in(['new_license', 'share_license'])],
            'user_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where(fn ($query) => $query->where('role', User::ROLE_USER)),
            ],
            'product_id' => ['required', 'integer', Rule::exists('products', 'id')],
            'sub_product_id' => ['nullable', 'integer', Rule::exists('products', 'id')],
            'no_license_key' => ['nullable', 'boolean'],
            'license_type_id' => ['required', 'integer', Rule::exists('license_types', 'id')],
            'quantity' => ['required', 'integer', 'min:1', 'max:999999'],
            'max_activations' => ['nullable', 'integer', 'min:1', 'max:999999'],
            'expired_date' => ['nullable', 'date'],
            'license_count' => ['required', 'integer', 'min:1', 'max:100'],
            'license_keys' => ['nullable', 'array'],
            'license_keys.*' => ['nullable', 'string', 'max:255'],
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
            'productOptions' => $this->productTreeBuilder->options(parentOnly: true),
            'licenseKeyLength' => \App\Models\Setting::get('license_key_length', 32),
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
