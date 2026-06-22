@props([
    'products',
    'depth' => 0,
    'parentName' => null,
    'parentPath' => null,
    'interactive' => false,
    'showContext' => false,
])

@php
    $collectBranch = function ($branchProduct, int $branchDepth) use (&$collectBranch): array {
        $records = [[
            'text' => $branchProduct->name.' '.$branchProduct->code,
            'status' => $branchProduct->is_active ? 'active' : 'inactive',
            'depth' => $branchDepth,
        ]];

        foreach ($branchProduct->allChildren as $childProduct) {
            $records = array_merge($records, $collectBranch($childProduct, $branchDepth + 1));
        }

        return $records;
    };
@endphp

<ul>
    @foreach ($products as $product)
        @php
            $hasChildren = $product->allChildren->isNotEmpty();
            $currentPath = $parentPath ? $parentPath.' / '.$product->name : null;
            $branchSearchText = '';
            $branchStatuses = [];
            $branchDepths = [];

            if ($interactive) {
                $branchRecords = collect($collectBranch($product, $depth));
                $branchSearchText = $branchRecords->pluck('text')->implode(' ');
                $branchStatuses = $branchRecords->pluck('status')->unique()->values()->all();
                $branchDepths = $branchRecords->pluck('depth')->unique()->values()->all();
            }
        @endphp

        <li
            class="relative pb-2 last:pb-0"
            data-tree-terminal="{{ $loop->last ? 'true' : 'false' }}"
            @if ($interactive)
                x-show="branchMatches(@js($branchSearchText), @js($branchStatuses), @js($branchDepths))"
                x-cloak
            @endif
        >
            @if ($depth > 0)
                <span
                    class="absolute -left-[22px] top-0 w-0.5 bg-madani-green/45 {{ $loop->last ? 'h-7' : 'bottom-0' }}"
                    aria-hidden="true"
                ></span>
                <span class="absolute -left-[22px] top-7 h-0.5 w-[22px] bg-madani-green/45" aria-hidden="true"></span>
            @endif

            <div class="relative rounded-xl border border-madani-border bg-white px-4 py-3 shadow-sm transition hover:border-madani-green/40">
                <div class="flex flex-wrap items-start justify-between gap-4" data-tree-depth="{{ $depth }}">
                    <div class="flex min-w-0 items-start gap-3">
                        @if ($interactive && $hasChildren)
                            <button
                                type="button"
                                class="mt-0.5 inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-madani-border bg-madani-ghost text-madani-deep transition hover:border-madani-green hover:text-madani-green madani-focus"
                                @click.stop.prevent="toggleBranch({{ $product->id }})"
                                :aria-expanded="(! isCollapsed({{ $product->id }})).toString()"
                            >
                                <span class="sr-only">Toggle {{ $product->name }}</span>
                                <svg
                                    class="h-4 w-4 transition-transform"
                                    :class="isCollapsed({{ $product->id }}) ? '-rotate-90' : 'rotate-0'"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                    aria-hidden="true"
                                >
                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        @elseif ($interactive)
                            <span class="mt-0.5 h-8 w-8 shrink-0 rounded-lg border border-transparent" aria-hidden="true"></span>
                        @endif

                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <p class="font-semibold text-madani-deep">{{ $product->name }}</p>
                                @if ($hasChildren)
                                    <span class="rounded-full bg-madani-ghost px-2.5 py-1 text-xs font-semibold text-madani-muted">
                                        {{ $product->allChildren->count() }}
                                    </span>
                                @endif
                            </div>
                            <p class="mt-1 text-xs font-semibold uppercase tracking-[0.14em] text-madani-muted">{{ $product->code }}</p>

                            @if ($showContext && ($parentName || $product->parent_id))
                                <p class="mt-2 text-sm text-madani-muted">
                                    Child of {{ $parentName ?? $product->parent?->name }}
                                </p>
                                <p class="mt-1 text-sm text-madani-muted">Path: {{ $currentPath ?? $product->getCatalogPath() }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center justify-end gap-2">
                        <x-status-badge :active="$product->is_active" />
                        <x-button variant="ghost" :href="route('admin.products.show', $product)">View</x-button>
                        <x-button variant="secondary" :href="route('admin.products.edit', $product)">Edit</x-button>
                        <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Delete this product?')">
                            @csrf
                            @method('DELETE')
                            <x-button variant="danger" class="gap-2 px-3 py-2">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M3 6h18" />
                                    <path d="M8 6V4h8v2" />
                                    <path d="M19 6l-1 14H6L5 6" />
                                    <path d="M10 11v5" />
                                    <path d="M14 11v5" />
                                </svg>
                                <span>Delete</span>
                            </x-button>
                        </form>
                    </div>
                </div>
            </div>

            @if ($hasChildren)
                <div
                    class="ml-7 mt-2 pl-5"
                    @if ($interactive)
                        x-show="! isCollapsed({{ $product->id }})"
                        x-cloak
                    @endif
                >
                    <x-product-tree
                        :products="$product->allChildren"
                        :depth="$depth + 1"
                        :parent-name="$product->name"
                        :parent-path="$currentPath"
                        :interactive="$interactive"
                        :show-context="$showContext"
                    />
                </div>
            @endif
        </li>
    @endforeach
</ul>
