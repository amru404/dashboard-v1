<?php

namespace App\Support;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

class ProductTreeBuilder
{
    /**
     * @return Collection<int, array{id: int, name: string, code: string, label: string, path: string, depth: int, is_active: bool}>
     */
    public function options(?Product $excludedProduct = null, bool $activeOnly = false): Collection
    {
        $excludedIds = collect();

        if ($excludedProduct) {
            $excludedIds = collect([$excludedProduct->id])
                ->merge($excludedProduct->getAllDescendantIds())
                ->map(fn (int $id): int => $id);
        }

        return $this->flattenProducts(
            Product::query()
                ->main()
                ->with('allChildren')
                ->orderBy('name')
                ->get(),
            $excludedIds,
            $activeOnly
        );
    }

    /**
     * @param  EloquentCollection<int, Product>  $products
     * @param  Collection<int, int>  $excludedIds
     * @param  array<int, string>  $parentPath
     * @return Collection<int, array{id: int, name: string, code: string, label: string, path: string, depth: int, is_active: bool}>
     */
    private function flattenProducts(EloquentCollection $products, Collection $excludedIds, bool $activeOnly, int $depth = 0, array $parentPath = []): Collection
    {
        return $products
            ->flatMap(function (Product $product) use ($excludedIds, $activeOnly, $depth, $parentPath): Collection {
                if ($excludedIds->contains($product->id)) {
                    return collect();
                }

                $path = [...$parentPath, $product->name];
                $children = $this->flattenProducts($product->allChildren, $excludedIds, $activeOnly, $depth + 1, $path);

                if ($activeOnly && ! $product->is_active) {
                    return $children;
                }

                return collect([[
                    'id' => $product->id,
                    'name' => $product->name,
                    'code' => $product->code,
                    'label' => str_repeat('-- ', $depth).$product->name,
                    'path' => implode(' / ', $path),
                    'depth' => $depth,
                    'is_active' => $product->is_active,
                ]])->merge($children);
            })
            ->values();
    }
}
