@props(['align' => 'right', 'width' => '48', 'contentClasses' => 'py-1 bg-white', 'openOnHover' => false])

@php
$alignmentClasses = match ($align) {
    'left' => 'ltr:origin-top-left rtl:origin-top-right start-0',
    'top' => 'origin-top',
    default => 'ltr:origin-top-right rtl:origin-top-left end-0',
};

$width = match ($width) {
    '48' => 'w-48',
    default => $width,
};
@endphp

<div
    class="relative"
    x-data="{
        open: false,
        closeTimer: null,
        show() {
            clearTimeout(this.closeTimer);
            this.open = true;
        },
        scheduleClose() {
            clearTimeout(this.closeTimer);
            this.closeTimer = setTimeout(() => this.open = false, 180);
        },
        toggle() {
            clearTimeout(this.closeTimer);
            this.open = ! this.open;
        },
    }"
    @click.outside="open = false"
    @close.stop="open = false"
    @keydown.escape.window="open = false"
    @if ($openOnHover)
        @mouseenter="show()"
        @mouseleave="scheduleClose()"
        @focusin="show()"
        @focusout="scheduleClose()"
    @endif
>
    <div @click="toggle()">
        {{ $trigger }}
    </div>

    <div x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="absolute top-full z-50 {{ $width }} rounded-md pt-2 {{ $alignmentClasses }}"
            style="display: none;"
            @mouseenter="show()"
            @mouseleave="scheduleClose()"
            @click="open = false">
        <div class="rounded-md shadow-lg ring-1 ring-black ring-opacity-5 {{ $contentClasses }}">
            {{ $content }}
        </div>
    </div>
</div>
