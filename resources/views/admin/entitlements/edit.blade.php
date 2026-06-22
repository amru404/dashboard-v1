@extends('layouts.admin')

@section('title', 'Edit Entitlement')

@section('content')
    <x-page-header title="Edit entitlement" subtitle="{{ $entitlement->user?->name }} - {{ $entitlement->product?->name }}">
        <x-slot name="actions">
            <x-button variant="secondary" :href="route('admin.entitlements.show', $entitlement)">Back to entitlement</x-button>
        </x-slot>
    </x-page-header>

    <x-card>
        <form method="POST" action="{{ route('admin.entitlements.update', $entitlement) }}">
            @method('PUT')
            @include('admin.entitlements._form', ['submitLabel' => 'Save changes'])
        </form>
    </x-card>
@endsection
