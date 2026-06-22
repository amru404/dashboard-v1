@extends('layouts.admin')

@section('title', 'Register Download')

@section('content')
    <x-page-header title="Register download" subtitle="Upload or register a private installer/product file for entitled customers." />

    <x-card>
        <form method="POST" action="{{ route('admin.download-items.store') }}" enctype="multipart/form-data">
            @include('admin.downloads._form', ['submitLabel' => 'Create download item'])
        </form>
    </x-card>
@endsection
