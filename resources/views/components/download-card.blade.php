@props([
    'downloadItem',
    'downloadUrl',
    'compact' => false,
    'layout' => 'grid',
])

@if ($compact)
    <div {{ $attributes->merge(['class' => 'rounded-lg border border-[#2a3f5f] bg-[#0f1829]/40 p-4']) }}>
        <p class="text-base font-semibold text-white">{{ $downloadItem->file_name }}</p>
        <p class="mt-1 text-sm text-gray-400">
            {{ $downloadItem->version ?? 'No version' }}
            <span class="mx-2">·</span>
            {{ number_format($downloadItem->file_size / 1048576, 2) }} MB
        </p>
        <a href="{{ $downloadUrl }}" class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-vd-primary hover:bg-vd-primary/90 text-white font-semibold text-sm transition-colors mt-4">
            <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
                Download
        </a>
    </div>
@else
    <div {{ $attributes->merge(['class' => 'vd-card border-[#2a3f5f] !p-6 '.($layout === 'list' ? 'flex flex-col' : 'flex flex-col')]) }}>
        <div class="flex flex-col gap-4 {{ $layout === 'list' ? 'md:flex-row md:items-start' : '' }} justify-between mb-4">
            <div class="flex-1">
                <h3 class="text-xl font-bold text-white mb-1">{{ $downloadItem->file_name }}</h3>
                <p class="text-xs tracking-[0.14em] text-vd-primary uppercase font-semibold">{{ $downloadItem->product->name }}</p>
            </div>
            @if ($downloadItem->isExpired())
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-500/20 text-red-400 border border-red-500/30">
                    Expired
                </span>
            @elseif ($downloadItem->is_active)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-500/20 text-green-400 border border-green-500/30">
                    ✓ Available
                </span>
            @else
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-500/20 text-gray-400 border border-gray-500/30">
                    Inactive
                </span>
            @endif
        </div>

        <p class="text-sm text-gray-400 mb-5">{{ $downloadItem->product->getCatalogPath() }}</p>

        <dl class="flex-1 space-y-3 text-sm {{ $layout === 'list' ? 'grid gap-3 sm:grid-cols-2' : '' }}">
            <div class="flex justify-between gap-4">
                <dt class="text-gray-400">Version</dt>
                <dd class="text-white font-medium">{{ $downloadItem->version ?? '—' }}</dd>
            </div>
            <div class="flex justify-between gap-4">
                <dt class="text-gray-400">File Size</dt>
                <dd class="text-cyan-400 font-semibold">
                    {{ $downloadItem->file_size ? number_format($downloadItem->file_size / 1048576, 2) . ' MB' : '—' }}
                </dd>
            </div>
            <div class="flex justify-between gap-4">
                <dt class="text-gray-400">Expires</dt>
                <dd class="text-white font-medium">
                    {{ $downloadItem->expired_date?->format('M j, Y') ?? 'Never' }}
                </dd>
            </div>
        </dl>

        <div class="mt-6 pt-4 border-t border-[#2a3f5f] {{ $layout === 'list' ? 'sm:mt-0 sm:pt-0 sm:border-t-0' : '' }}">
            <a href="{{ $downloadUrl }}" class="inline-flex items-center justify-center w-full px-4 py-2.5 rounded-lg bg-vd-primary hover:bg-vd-primary/90 text-white font-semibold text-sm transition-colors">
                <x-primary-button>
                    <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download File
                </x-primary-button>
            </a>
        </div>
    </div>
@endif
