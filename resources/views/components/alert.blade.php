@if (session('status'))
    <div class="mb-6 rounded-lg border border-madani-success/20 bg-madani-pale px-4 py-3 text-sm font-medium text-madani-deep">
        {{ session('status') }}
    </div>
@endif

@if ($errors->any())
    <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
        {{ $errors->first() }}
    </div>
@endif
