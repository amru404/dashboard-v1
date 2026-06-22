<x-product-tree
    :products="$products"
    :depth="$depth ?? 0"
    :parent-name="$parentName ?? null"
    :interactive="$interactive ?? false"
    :show-context="$showContext ?? false"
/>
