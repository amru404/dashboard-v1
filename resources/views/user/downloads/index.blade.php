@extends('layouts.user')

@section('title', 'My Downloads')

@section('content')
    <x-page-header
        title="My Downloads"
        subtitle="Files available through your active product entitlements. Downloads are streamed securely."
    />

    {{-- Filters --}}
    <div class="vd-card mb-6">
        <form method="GET" action="{{ route('user.downloads.index') }}"
              class="grid gap-4 sm:grid-cols-2 lg:grid-cols-[1fr_1fr_auto]">
            <div>
                <x-form-label for="search" value="Search" />
                <x-form-input id="search" name="search"
                    value="{{ request('search') }}"
                    placeholder="File name or product name" class="mt-1" />
            </div>
            <div>
                <x-form-label for="product_id" value="Product" />
                <select id="product_id" name="product_id" class="vd-input mt-1">
                    <option value="">All products</option>
                    @foreach ($filterProducts ?? [] as $fp)
                        <option value="{{ $fp->id }}" @selected(request('product_id') == $fp->id)>
                            {{ $fp->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <x-button type="submit">Filter</x-button>
                <x-button variant="secondary" :href="route('user.downloads.index')">Clear</x-button>
            </div>
        </form>
    </div>

    {{-- Download cards grid --}}
    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
        @forelse ($downloadItems as $downloadItem)
            <x-download-card
                :download-item="$downloadItem"
                :download-url="route('user.downloads.download', $downloadItem)" />
        @empty
            <div class="vd-card md:col-span-2 xl:col-span-3 py-12 text-center">
                <svg class="mx-auto mb-3 h-10 w-10 text-vd-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                </svg>
                <p class="text-body-sm text-vd-muted">No downloads are currently available for your account.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $downloadItems->links() }}
    </div>
@endsection
