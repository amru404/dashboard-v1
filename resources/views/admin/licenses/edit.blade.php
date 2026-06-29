@extends('layouts.admin')

@section('title', 'Edit License')

@section('content')
    <x-page-header title="Edit license" subtitle="{{ $license->masked_license_key }}">
        <x-slot name="actions">
            <x-button variant="secondary" :href="route('admin.licenses.show', $license)">Back to license</x-button>
        </x-slot>
    </x-page-header>

    <x-card>
        <form method="POST" action="{{ route('admin.licenses.update', $license) }}" x-data="{ licenseMode: 'new_license', noLicenseKey: false }">
            @method('PUT')
            @include('admin.licenses._form', ['submitLabel' => 'Save changes'])
        </form>
    </x-card>
@endsection
