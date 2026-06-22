@extends('layouts.admin')

@section('title', 'Edit Download')

@section('content')
    <x-page-header title="Edit download" subtitle="{{ $downloadItem->file_name }}">
        <x-slot name="actions">
            <x-button variant="secondary" :href="route('admin.download-items.show', $downloadItem)">Back to download</x-button>
        </x-slot>
    </x-page-header>

    <x-card>
        <form method="POST" action="{{ route('admin.download-items.update', $downloadItem) }}" enctype="multipart/form-data">
            @method('PUT')
            @include('admin.downloads._form', ['submitLabel' => 'Save changes'])
        </form>
    </x-card>
@endsection
