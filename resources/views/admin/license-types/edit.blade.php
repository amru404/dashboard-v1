@extends('layouts.admin')

@section('title', 'Edit License Type')

@section('content')
    <x-page-header title="Edit license type" subtitle="{{ $licenseType->name }}" />

    <x-card>
        <form method="POST" action="{{ route('admin.license-types.update', $licenseType) }}">
            @method('PUT')
            @include('admin.license-types._form', ['submitLabel' => 'Save changes'])
        </form>
    </x-card>
@endsection
