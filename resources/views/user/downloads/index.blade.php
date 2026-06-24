@extends('layouts.user')

@section('title', 'My Downloads')

@section('content')

{{-- ── Page Header ── --}}
<div class="mb-8">
    <h1 class="text-3xl font-bold text-white mb-2">Downloads</h1>
    <p class="text-base text-gray-300">
        Files available through your active product entitlements. Downloads are streamed securely.
    </p>
</div>

{{-- ── Filters Card ── --}}
<div class="vd-card  border-[#2a3f5f] !p-6 mb-6">
    <form method="GET" action="{{ route('user.downloads.index') }}"
          class="grid gap-4 sm:grid-cols-2 lg:grid-cols-[1fr_1fr_auto]">
        <div>
            <label for="search" class="block text-sm font-semibold text-white mb-2">Search</label>
            <input type="text" 
                   id="search" 
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="File name or product name"
                   class="w-full px-4 py-2.5 rounded-lg bg-[#0f1829] border border-[#2a3f5f] text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-vd-primary/50 focus:border-vd-primary transition-colors" />
        </div>
        <div>
            <label for="product_id" class="block text-sm font-semibold text-white mb-2">Product</label>
            <select id="product_id" 
                    name="product_id" 
                    class="w-full px-4 py-2.5 rounded-lg bg-[#0f1829] border border-[#2a3f5f] text-white focus:outline-none focus:ring-2 focus:ring-vd-primary/50 focus:border-vd-primary transition-colors">
                <option value="">All products</option>
                @foreach ($filterProducts ?? [] as $fp)
                    <option value="{{ $fp->id }}" @selected(request('product_id') == $fp->id)>
                        {{ $fp->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="inline-flex items-center justify-center px-6 py-2.5 rounded-lg bg-vd-primary hover:bg-vd-primary/90 text-white font-semibold text-sm transition-colors">
                Filter
            </button>
            <a href="{{ route('user.downloads.index') }}" class="inline-flex items-center justify-center px-6 py-2.5 rounded-lg bg-white/10 hover:bg-white/15 text-white font-semibold text-sm border border-white/20 transition-colors">
                Clear
            </a>
        </div>
    </form>
</div>

{{-- ── Download Cards Grid ── --}}
<div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
    @forelse ($downloadItems as $downloadItem)
        <x-download-card
            :download-item="$downloadItem"
            :download-url="route('user.downloads.download', $downloadItem)" />
    @empty
        <div class="vd-card  border-[#2a3f5f] md:col-span-2 xl:col-span-3 !p-12 text-center">
            <svg class="mx-auto mb-4 h-12 w-12 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
            </svg>
            <p class="text-gray-400">No downloads are currently available for your account.</p>
        </div>
    @endforelse
</div>

{{-- ── Pagination ── --}}
@if ($downloadItems->hasPages())
    <div class="mt-8">
        {{ $downloadItems->links() }}
    </div>
@endif

@endsection
