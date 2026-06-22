@extends('layouts.user')

@section('title', 'My Downloads')

@section('content')
    <x-page-header title="My downloads" subtitle="Files available through your active product entitlements." />

    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
        @forelse ($downloadItems as $downloadItem)
            <x-download-card :download-item="$downloadItem" :download-url="route('user.downloads.download', $downloadItem)" />
        @empty
            <x-card class="md:col-span-2 xl:col-span-3">
                <p class="text-sm text-madani-muted">No downloads are currently available.</p>
            </x-card>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $downloadItems->links() }}
    </div>
@endsection
