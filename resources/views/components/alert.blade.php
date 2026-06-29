@if (session('status') || session('success'))
    <div class="mb-6 flex items-start gap-3 rounded-lg border border-vd-success/25 bg-vd-success/10 px-4 py-3 text-body-sm text-vd-success">
        <svg class="mt-0.5 h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
        </svg>
        <span>{{ session('status') ?? session('success') }}</span>
    </div>
@endif

@if (session('error'))
    <div class="mb-6 flex items-start gap-3 rounded-lg border border-vd-error/25 bg-vd-error/10 px-4 py-3 text-body-sm text-vd-error">
        <svg class="mt-0.5 h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        <span>{{ session('error') }}</span>
    </div>
@endif

@if (session('sub_product_errors') && count(session('sub_product_errors')) > 0)
    <div class="mb-6 flex items-start gap-3 rounded-lg border border-vd-error/25 bg-vd-error/10 px-4 py-3 text-body-sm text-vd-error">
        <svg class="mt-0.5 h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
        </svg>
        <div>
            <p class="font-semibold mb-1">Some sub-products could not be saved:</p>
            <ul class="space-y-1 ml-4 list-disc">
                @foreach (session('sub_product_errors') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

@if ($errors->any())
    <div class="mb-6 flex items-start gap-3 rounded-lg border border-vd-error/25 bg-vd-error/10 px-4 py-3 text-body-sm text-vd-error">
        <svg class="mt-0.5 h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
        </svg>
        <span>{{ $errors->first() }}</span>
    </div>
@endif
