<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'code',
        'name',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Product, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'parent_id');
    }

    /**
     * @return HasMany<Product, $this>
     */
    public function subProducts(): HasMany
    {
        return $this->hasMany(Product::class, 'parent_id')->orderBy('name');
    }

    /**
     * @return HasMany<Product, $this>
     */
    public function allChildren(): HasMany
    {
        return $this->subProducts()->with('allChildren');
    }

    /**
     * @return HasMany<Product, $this>
     */
    public function children(): HasMany
    {
        return $this->subProducts();
    }

    /**
     * @return HasMany<Product, $this>
     */
    public function childrenRecursive(): HasMany
    {
        return $this->allChildren();
    }

    /**
     * @return Collection<int, Product>
     */
    public function childrenTree(): Collection
    {
        return $this->relationLoaded('allChildren') ? $this->allChildren : $this->subProducts;
    }

    public function totalLicenseCount(): int
    {
        $count = $this->relationLoaded('licenses') ? $this->licenses->count() : $this->licenses()->count();

        if (! $this->relationLoaded('allChildren')) {
            return $count;
        }

        return $count + $this->allChildren->sum(fn (Product $child) => $child->totalLicenseCount());
    }

    /**
     * @return HasMany<License, $this>
     */
    public function licenses(): HasMany
    {
        return $this->hasMany(License::class);
    }

    /**
     * @return HasMany<Entitlement, $this>
     */
    public function entitlements(): HasMany
    {
        return $this->hasMany(Entitlement::class);
    }

    /**
     * @return HasMany<DownloadItem, $this>
     */
    public function downloadItems(): HasMany
    {
        return $this->hasMany(DownloadItem::class);
    }

    /**
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    public function scopeRoot(Builder $query): Builder
    {
        return $this->scopeMain($query);
    }

    /**
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    public function scopeMain(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    /**
     * @return array<int, int>
     */
    public function getAllDescendantIds(): array
    {
        $visited = $this->exists ? [$this->id => true] : [];

        return $this->collectDescendantIds($visited);
    }

    /**
     * @return array<int, int>
     */
    public function getAncestorIds(): array
    {
        $ancestors = [];
        $parent = $this->parent;

        while ($parent) {
            $ancestors[] = $parent->id;
            $parent = $parent->parent;
        }

        return $ancestors;
    }

    /**
     * @return array<int, int>
     */
    public function getAncestorIdsAndSelf(): array
    {
        return array_merge([$this->id], $this->getAncestorIds());
    }

    /**
     * @return Collection<int, Product>
     */
    public function getFlatDescendants(int $depth = 0): Collection
    {
        $visited = $this->exists ? [$this->id => true] : [];

        return $this->collectFlatDescendants($depth, $visited);
    }

    /**
     * @return Collection<int, Product>
     */
    public function getBreadcrumbs(): Collection
    {
        $trail = collect();
        $visited = [];
        $current = $this;

        while ($current && $current->exists && ! isset($visited[$current->id])) {
            $visited[$current->id] = true;
            $trail->prepend($current);
            $current = $current->parent;
        }

        return $trail->values();
    }

    public function getCatalogPath(string $separator = ' / '): string
    {
        return $this->getBreadcrumbs()
            ->pluck('name')
            ->implode($separator);
    }

    /**
     * @param  array<int, bool>  $visited
     * @return array<int, int>
     */
    private function collectDescendantIds(array &$visited): array
    {
        $ids = [];

        foreach ($this->subProducts()->get() as $child) {
            if (isset($visited[$child->id])) {
                continue;
            }

            $visited[$child->id] = true;
            $ids[] = $child->id;
            $ids = array_merge($ids, $child->collectDescendantIds($visited));
        }

        return array_values(array_unique($ids));
    }

    /**
     * @param  array<int, bool>  $visited
     * @return Collection<int, Product>
     */
    private function collectFlatDescendants(int $depth, array &$visited): Collection
    {
        return $this->subProducts()
            ->get()
            ->flatMap(function (Product $child) use ($depth, &$visited): Collection {
                if (isset($visited[$child->id])) {
                    return collect();
                }

                $visited[$child->id] = true;
                $child->setAttribute('tree_depth', $depth);

                return collect([$child])->merge($child->collectFlatDescendants($depth + 1, $visited));
            })
            ->values();
    }
}
