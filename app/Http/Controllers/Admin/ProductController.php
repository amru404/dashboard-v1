<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Support\ProductTreeBuilder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function __construct(private readonly ProductTreeBuilder $productTreeBuilder)
    {
    }

    public function index(): View
    {
        return view('admin.products.index', [
            'rootProducts' => Product::query()
                ->main()
                ->with('allChildren')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function create(Request $request): View
    {
        return view('admin.products.create', [
            'product' => new Product([
                'parent_id' => $request->integer('parent_id') ?: null,
                'is_active' => true,
            ]),
            'parentOptions' => $this->productTreeBuilder->options(activeOnly: true),
            'existingProductIds' => $this->existingProductIds(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        $product = Product::query()->create($data);

        return redirect()
            ->route('admin.products.show', $product)
            ->with('status', 'Product created.');
    }

    public function show(Product $product): View
    {
        $product->load(['parent', 'allChildren'])
            ->loadCount(['subProducts', 'licenses', 'entitlements', 'downloadItems']);

        $breadcrumbs = $product->getBreadcrumbs();

        return view('admin.products.show', [
            'product' => $product,
            'breadcrumbs' => $breadcrumbs,
            'catalogPath' => $breadcrumbs->pluck('name')->implode(' / '),
        ]);
    }

    public function edit(Product $product): View
    {
        return view('admin.products.edit', [
            'product' => $product,
            'parentOptions' => $this->productTreeBuilder->options($product, activeOnly: true),
            'existingProductIds' => $this->existingProductIds($product),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $this->validatedData($request, $product);

        if ($this->parentWouldCreateCycle($product, $data['parent_id'])) {
            return back()
                ->withErrors(['parent_id' => 'A product cannot be assigned to itself or one of its descendants.'])
                ->withInput();
        }

        $product->update($data);

        return redirect()
            ->route('admin.products.show', $product)
            ->with('status', 'Product updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->loadCount(['subProducts', 'licenses', 'entitlements', 'downloadItems']);

        if (
            $product->sub_products_count > 0 ||
            $product->licenses_count > 0 ||
            $product->entitlements_count > 0 ||
            $product->download_items_count > 0
        ) {
            return redirect()
                ->route('admin.products.show', $product)
                ->withErrors(['product' => 'Products with child products, licenses, entitlements, or downloads cannot be deleted. Mark the product inactive instead.']);
        }

        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('status', 'Product deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedData(Request $request, ?Product $product = null): array
    {
        $rawProductId = (string) $request->input('code', '');
        $normalizedProductId = $this->normalizeProductId($rawProductId);

        if (filled($rawProductId) && $normalizedProductId === '') {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'code' => 'The product ID must contain letters or numbers.',
            ]);
        }

        $request->merge([
            'code' => $normalizedProductId,
        ]);

        $data = $request->validate([
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('products', 'id')->where(fn ($query) => $query->where('is_active', true)),
            ],
            'code' => [
                'nullable',
                'string',
                'max:255',
                function (string $attribute, mixed $value, \Closure $fail) use ($product, $rawProductId): void {
                    if (filled($rawProductId) && blank($value)) {
                        $fail('The product ID must contain letters or numbers.');

                        return;
                    }

                    if (blank($value)) {
                        return;
                    }

                    $exists = Product::query()
                        ->where('code', (string) $value)
                        ->when($product, fn ($query) => $query->whereKeyNot($product->getKey()))
                        ->exists();

                    if ($exists) {
                        $fail('This product ID is already in use.');
                    }
                },
            ],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['parent_id'] = blank($data['parent_id'] ?? null) ? null : (int) $data['parent_id'];

        if (blank($data['code'] ?? null)) {
            if ($product) {
                unset($data['code']);
            } else {
                $data['code'] = $this->uniqueCodeFromName($data['name']);
            }
        }

        return $data;
    }

    private function parentWouldCreateCycle(Product $product, ?int $parentId): bool
    {
        if ($parentId === null) {
            return false;
        }

        $invalidParentIds = array_merge([$product->id], $product->getAllDescendantIds());

        return in_array($parentId, $invalidParentIds, true);
    }

    private function uniqueCodeFromName(string $name): string
    {
        $baseCode = $this->normalizeProductId($name);

        if ($baseCode === '') {
            $baseCode = 'PRODUCT';
        }

        $code = $baseCode;
        $suffix = 2;

        while (Product::query()->where('code', $code)->exists()) {
            $code = "{$baseCode}-{$suffix}";
            $suffix++;
        }

        return $code;
    }

    private function normalizeProductId(string $productId): string
    {
        $productId = Str::upper(Str::ascii($productId));

        return trim(preg_replace('/[^A-Z0-9]+/', '-', $productId) ?? '', '-');
    }

    /**
     * @return array<int, string>
     */
    private function existingProductIds(?Product $excludedProduct = null): array
    {
        return Product::query()
            ->when($excludedProduct, fn ($query) => $query->whereKeyNot($excludedProduct->getKey()))
            ->pluck('code')
            ->all();
    }
}
