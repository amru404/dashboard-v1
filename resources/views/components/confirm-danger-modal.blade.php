@props([
    'id',
    'title' => 'Confirm action',
    'message' => 'This action cannot be undone.',
    'confirmLabel' => 'Confirm',
])

<div id="{{ $id }}" {{ $attributes->merge(['class' => 'fixed inset-0 z-50 hidden items-center justify-center bg-madani-depth/70 px-4']) }}>
    <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl">
        <h2 class="text-xl font-bold text-madani-deep">{{ $title }}</h2>
        <p class="mt-3 text-sm leading-6 text-madani-muted">{{ $message }}</p>

        <div class="mt-6 flex flex-wrap justify-end gap-3">
            {{ $slot }}
            <button type="button" data-modal-close="{{ $id }}" class="inline-flex items-center justify-center rounded-lg border border-madani-deep px-5 py-3 text-sm font-semibold text-madani-deep transition hover:bg-madani-deep hover:text-white">
                Cancel
            </button>
            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-red-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-red-800">
                {{ $confirmLabel }}
            </button>
        </div>
    </div>
</div>
