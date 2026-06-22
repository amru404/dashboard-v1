@extends('layouts.admin')

@section('title', 'Create User')

@section('content')
    <x-page-header title="Create user" subtitle="Add an admin or customer account." />

    <x-card>
        <form method="POST" action="{{ route('admin.users.store') }}">
            @include('admin.users._form', ['submitLabel' => 'Create user'])
        </form>
    </x-card>
@endsection
