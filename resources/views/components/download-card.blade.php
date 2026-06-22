@props([
    'downloadItem',
    'downloadUrl',
    'compact' => false,
])

@if ($compact)
    <div {{ $attributes->merge(['class' => 'rounded-xl border border-madani-border bg-madani-ghost p-4']) }}>
        <p class="font-semibold text-madani-deep">{{ $downloadItem->file_name }}</p>
        <p class="mt-1 text-sm text-madani-muted">{{ $downloadItem->version ?? 'No version' }} - {{ number_format($downloadItem->file_size / 1048576, 2) }} MB</p>
        <x-button class="mt-4" :href="$downloadUrl">Download</x-button>
    </div>
@else
    <x-card {{ $attributes }}>
        <x-status-badge :active="$downloadItem->is_active && ! $downloadItem->isExpired()">
            {{ $downloadItem->isExpired() ? 'Expired' : ($downloadItem->is_active ? 'Active' : 'Inactive') }}
        </x-status-badge>
        <h2 class="mt-4 text-lg font-bold text-madani-deep">{{ $downloadItem->file_name }}</h2>
        <p class="mt-1 text-sm font-semibold text-madani-muted">{{ $downloadItem->product->name }}</p>
        <p class="mt-2 text-sm text-madani-muted">{{ $downloadItem->product->getCatalogPath() }}</p>
        <dl class="mt-5 space-y-3 text-sm">
            <div class="flex justify-between gap-4">
                <dt class="text-madani-muted">Version</dt>
                <dd class="font-semibold text-madani-deep">{{ $downloadItem->version ?? 'No version' }}</dd>
            </div>
            <div class="flex justify-between gap-4">
                <dt class="text-madani-muted">Size</dt>
                <dd class="font-semibold text-madani-deep">{{ number_format($downloadItem->file_size / 1048576, 2) }} MB</dd>
            </div>
            <div class="flex justify-between gap-4">
                <dt class="text-madani-muted">Expires</dt>
                <dd class="font-semibold text-madani-deep">{{ $downloadItem->expired_date?->format('M j, Y') ?? 'Never' }}</dd>
            </div>
        </dl>
        <div class="mt-6">
            <x-button :href="$downloadUrl">Download</x-button>
        </div>
    </x-card>
@endif
