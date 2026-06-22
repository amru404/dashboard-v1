@extends('layouts.admin')

@section('title', 'Edit Organization')

@section('content')
    <x-page-header title="Edit organization" subtitle="{{ $organization->name }}" />

    <x-card>
        <form method="POST" action="{{ route('admin.organizations.update', $organization) }}">
            @method('PUT')
            @include('admin.organizations._form', ['submitLabel' => 'Save changes'])
        </form>
    </x-card>
@endsection
