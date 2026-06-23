@props([
    'downloadItem',
    'downloadUrl',
    'compact' => false,
])

@if ($compact)
    <div {{ $attributes->merge(['class' => 'rounded-lg border border-vd-border bg-vd-secondary/40 p-4']) }}>
        <p class="text-label-md text-vd-on-surface">{{ $downloadItem->file_name }}</p>
        <p class="mt-1 text-body-sm text-vd-muted">
            {{ $downloadItem->version ?? 'No version' }}
            &mdash;
            {{ number_format($downloadItem->file_size / 1048576, 2) }} MB
        </p>
        <a href="{{ $downloadUrl }}" class="vd-btn-primary mt-4 inline-flex">Download</a>
    </div>
@else
    <div {{ $attributes->merge(['class' => 'vd-card flex flex-col']) }}>
        <div class="flex items-start justify-between gap-3 mb-4">
            <x-badge :active="$downloadItem->is_active && ! $downloadItem->isExpired()">
                {{ $downloadItem->isExpired() ? 'Expired' : ($downloadItem->is_active ? 'Active' : 'Inactive') }}
            </x-badge>
        </div>

        <h2 class="text-label-lg text-vd-on-surface">{{ $downloadItem->file_name }}</h2>
        <p class="mt-1 text-label-sm text-vd-primary">{{ $downloadItem->product->name }}</p>
        <p class="mt-1 text-body-sm text-vd-muted">{{ $downloadItem->product->getCatalogPath() }}</p>

        <dl class="mt-5 space-y-2.5 text-body-sm flex-1">
            <div class="flex justify-between gap-4">
                <dt class="text-vd-muted">Version</dt>
                <dd class="text-vd-on-surface font-medium">{{ $downloadItem->version ?? '—' }}</dd>
            </div>
            <div class="flex justify-between gap-4">
                <dt class="text-vd-muted">Size</dt>
                <dd class="text-vd-on-surface font-medium">
                    {{ $downloadItem->file_size ? number_format($downloadItem->file_size / 1048576, 2) . ' MB' : '—' }}
                </dd>
            </div>
            <div class="flex justify-between gap-4">
                <dt class="text-vd-muted">Expires</dt>
                <dd class="text-vd-on-surface font-medium">
                    {{ $downloadItem->expired_date?->format('M j, Y') ?? 'Never' }}
                </dd>
            </div>
        </dl>

        <div class="mt-5 pt-4 border-t border-vd-border">
            <a href="{{ $downloadUrl }}" class="vd-btn-primary w-full justify-center">
                <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Download
            </a>
        </div>
    </div>
@endif
