@props(['title', 'subtitle' => null])

<div {{ $attributes->merge(['class' => 'mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between']) }}>
    <div>
        <h1 class="text-3xl font-extrabold leading-tight text-madani-deep">{{ $title }}</h1>
        @if ($subtitle)
            <p class="mt-2 max-w-3xl text-sm leading-6 text-madani-muted">{{ $subtitle }}</p>
        @endif
    </div>

    @isset($actions)
        <div class="flex flex-wrap items-center gap-3">
            {{ $actions }}
        </div>
    @endisset
</div>
