@extends('layouts.admin')

@section('title', 'Create Organization')

@section('content')
    <x-page-header title="Create organization" subtitle="Add a new organization for account assignment." />

    <x-card>
        <form method="POST" action="{{ route('admin.organizations.store') }}">
            @include('admin.organizations._form', ['submitLabel' => 'Create organization'])
        </form>
    </x-card>
@endsection
