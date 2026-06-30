@extends('layouts.admin')

@section('title', 'New Entitlement')

@section('content')
    <x-page-header title="Grant entitlement" subtitle="Give a customer access to a product and its controlled downloads." />

    <x-card>
        <form method="POST" action="{{ route('admin.entitlements.store') }}">
            @include('admin.entitlements._form', ['submitLabel' => 'Grant entitlement'])
        </form>
    </x-card>
@endsection


