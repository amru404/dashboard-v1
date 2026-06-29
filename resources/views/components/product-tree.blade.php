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

<div class="space-y-2">
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

        <div
            @if ($interactive)
                x-show="branchMatches(@js($branchSearchText), @js($branchStatuses), @js($branchDepths))"
                x-cloak
            @endif
            :style="{ 'margin-left': '{{ $depth * 1.5 }}rem' }"
            class="border border-[#2a3f5f] rounded-lg overflow-hidden bg-[#0f1829]/50 transition-all"
        >
            {{-- Accordion Header with Actions --}}
            <div class="px-4 py-3 flex items-center justify-between hover:bg-white/5 transition-colors gap-4">
                <button
                    type="button"
                    class="flex items-center gap-3 min-w-0 flex-1 text-left"
                    @if ($hasChildren && $interactive)
                        @click="toggleBranch({{ $product->id }})"
                        :aria-expanded="(! isCollapsed({{ $product->id }})).toString()"
                    @endif
                >
                    @if ($hasChildren && $interactive)
                        <svg
                            class="h-5 w-5 text-gray-400 shrink-0 transition-transform"
                            :class="isCollapsed({{ $product->id }}) ? '' : 'rotate-180'"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    @elseif ($hasChildren)
                        <div class="w-5 h-5 shrink-0"></div>
                    @endif
                    
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="text-sm font-semibold text-white">{{ $product->name }}</h3>
                            @if ($hasChildren)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-vd-primary/10 text-vd-primary border border-vd-primary/20">
                                    {{ $product->allChildren->count() }} sub
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-400 uppercase tracking-wider font-mono mt-1">{{ $product->code }}</p>
                    </div>
                </button>

                {{-- Status Badge + Action Buttons --}}
                <div class="flex items-center gap-2 ml-2 shrink-0">
                    @if ($product->is_active)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-500/20 text-green-400 border border-green-500/30">
                            Active
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-500/20 text-gray-400 border border-gray-500/30">
                            Inactive
                        </span>
                    @endif
                    
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.products.show', $product) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-gray-300 hover:text-white transition-colors">
                            View
                        </a>
                        <a href="{{ route('admin.products.edit', $product) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-gray-300 hover:text-white transition-colors">
                            Edit
                        </a>
                        <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Delete this product?')" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-vd-error hover:text-vd-error transition-colors">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Accordion Content (Child Products) --}}
            @if ($hasChildren)
                <div
                    @if ($interactive)
                        x-show="! isCollapsed({{ $product->id }})"
                        x-cloak
                    @endif
                    class="border-t border-[#2a3f5f] bg-[#0f1829]/20 px-4 py-3"
                >
                <div class="space-y-2">
                        <x-product-tree
                            :products="$product->allChildren"
                            :depth="$depth + 1"
                            :parent-name="$product->name"
                            :parent-path="$currentPath"
                            :interactive="$interactive"
                            :show-context="$showContext"
                        />
                    </div>
                </div>
            @endif
        </div>
    @endforeach
</div>
