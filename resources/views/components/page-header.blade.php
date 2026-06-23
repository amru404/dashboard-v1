@props(['title', 'subtitle' => null, 'role' => null])

<div {{ $attributes->merge(['class' => 'mb-8']) }}>
    @if ($role)
        <p class="mb-2 text-eyebrow tracking-[0.18em] text-vd-primary uppercase">{{ $role }} Portal</p>
    @endif

    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-headline-md text-vd-on-surface">{{ $title }}</h1>
            @if ($subtitle)
                <p class="mt-2 max-w-2xl text-body-sm text-vd-muted leading-relaxed">{{ $subtitle }}</p>
            @endif
        </div>

        @isset($actions)
            <div class="flex flex-wrap items-center gap-3 shrink-0">
                {{ $actions }}
            </div>
        @endisset
    </div>
</div>
